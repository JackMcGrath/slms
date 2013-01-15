<?php


/**
 * Generates a PHP5 base Object class for user object model (OM).
 * Fixes: valid events triggering
 *
 * Notes: DON'T USE NAMESPACES because propel has no psr-0 autoloader
 *
 *
 */
class PHP5ObjectBuilderWithValidEvents extends \PHP5ObjectBuilder
{
    /**
     * Adds the workhourse doSave() method.
     * @param string &$script The script will be modified in this method.
     */
    protected function addDoSave(&$script)
    {
        $table = $this->getTable();

        $reloadOnUpdate = $table->isReloadOnUpdate();
        $reloadOnInsert = $table->isReloadOnInsert();

        $script .= "
    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO \$con";
        if ($reloadOnUpdate || $reloadOnInsert) {
            $script .= "
     * @param boolean \$skipReload Whether to skip the reload for this object from database.";
        }
        $script .= "
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO \$con".($reloadOnUpdate || $reloadOnInsert ? ", \$skipReload = false" : "").")
    {
        \$affectedRows = 0; // initialize var to track total num of affected rows
        if (!\$this->alreadyInSave) {
            \$this->alreadyInSave = true;
            \$isInsert = \$this->isNew();
            \$isUpdate = \$this->isModified();
";
        if ($reloadOnInsert || $reloadOnUpdate) {
            $script .= "
            \$reloadObject = false;
";
        }

        if (count($table->getForeignKeys())) {

            $script .= "
            // We call the save method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.
";

            foreach ($table->getForeignKeys() as $fk) {
                $aVarName = $this->getFKVarName($fk);
                $script .= "
            if (\$this->$aVarName !== null) {
                if (\$this->" . $aVarName . "->isModified() || \$this->" . $aVarName . "->isNew()) {
                    \$affectedRows += \$this->" . $aVarName . "->save(\$con);
                }
                \$this->set".$this->getFKPhpNameAffix($fk, $plural = false)."(\$this->$aVarName);
            }
";
            } // foreach foreign k
        } // if (count(foreign keys))

//            $script .= "
//                if (\$isInsert) {
//                    \$this->postInsert(\$con);";
//            $this->applyBehaviorModifier('postInsert', $script, "					");
//            $script .= "
//                } else {
//                    \$this->postUpdate(\$con);";
//            $this->applyBehaviorModifier('postUpdate', $script, "					");
//            $script .= "
//                }
//                \$this->postSave(\$con);";
//                $this->applyBehaviorModifier('postSave', $script, "				");

        $script .= "
            if (\$this->isNew() || \$this->isModified()) {
                // persist changes
                if (\$this->isNew()) {
                    \$this->doInsert(\$con);";
        if ($reloadOnInsert) {
            $script .= "
                    if (!\$skipReload) {
                        \$reloadObject = true;
                    }";

        }
        $script .= "
                } else {
                    \$this->doUpdate(\$con);";
        if ($reloadOnUpdate) {
            $script .= "
                    if (!\$skipReload) {
                        \$reloadObject = true;
                    } ";
        }
        $script .= "
                }
                \$affectedRows += 1;";

        // We need to rewind any LOB columns
        foreach ($table->getColumns() as $col) {
            $clo = strtolower($col->getName());
            if ($col->isLobType()) {
                $script .= "
                // Rewind the $clo LOB column, since PDO does not rewind after inserting value.
                if (\$this->$clo !== null && is_resource(\$this->$clo)) {
                    rewind(\$this->$clo);
                }
";
            }
        }

        $script .= "
                \$this->resetModified();
            }
";

        if ($table->hasCrossForeignKeys()) {
            foreach ($table->getCrossFks() as $fkList) {
                list($refFK, $crossFK) = $fkList;
                $this->addCrossFkScheduledForDeletion($script, $refFK, $crossFK);
            }
        }

        foreach ($table->getReferrers() as $refFK) {
            $this->addRefFkScheduledForDeletion($script, $refFK);

            if ($refFK->isLocalPrimaryKey()) {
                $varName = $this->getPKRefFKVarName($refFK);
                $script .= "
            if (\$this->$varName !== null) {
                if (!\$this->{$varName}->isDeleted() && (\$this->{$varName}->isNew() || \$this->{$varName}->isModified())) {
                        \$affectedRows += \$this->{$varName}->save(\$con);
                }
            }
";
            } else {
                $collName = $this->getRefFKCollVarName($refFK);
                $script .= "
            if (\$this->$collName !== null) {
                foreach (\$this->$collName as \$referrerFK) {
                    if (!\$referrerFK->isDeleted() && (\$referrerFK->isNew() || \$referrerFK->isModified())) {
                        \$affectedRows += \$referrerFK->save(\$con);
                    }
                }
            }
";
            } // if refFK->isLocalPrimaryKey()

        } /* foreach getReferrers() */

        $script .= "
            \$this->alreadyInSave = false;
";
        if ($reloadOnInsert || $reloadOnUpdate) {
            $script .= "
            if (\$reloadObject) {
                \$this->reload(\$con);
            }
";
        }
        $script .= "
            if (\$isInsert) {
                \$this->postInsert(\$con);";
        $this->applyBehaviorModifier('postInsert', $script, "					");
        $script .= "
            }";

        $script .= "
            if (\$isUpdate) {
                \$this->postUpdate(\$con);";
        $this->applyBehaviorModifier('postUpdate', $script, "					");
        $script .= "
            }";

        $script .= "
            if (\$isUpdate || \$isInsert) {
                \$this->postSave(\$con);";
        $this->applyBehaviorModifier('postSave', $script, "					");
        $script .= "
            }";

        $script .= "
        }

        return \$affectedRows;
    } // doSave()
";

    }

    /**
     * Adds the function body for the save method
     * @param string &$script The script will be modified in this method.
     * @see        addSave()
     **/
    protected function addSaveBody(&$script)
    {
        $table = $this->getTable();
        $reloadOnUpdate = $table->isReloadOnUpdate();
        $reloadOnInsert = $table->isReloadOnInsert();

        $script .= "
        if (\$this->isDeleted()) {
            throw new PropelException(\"You cannot save an object that has been deleted.\");
        }

        if (\$con === null) {
            \$con = Propel::getConnection(".$this->getPeerClassname()."::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        \$con->beginTransaction();
        // \$isInsert = \$this->isNew();
        try {";

        if ($this->getGeneratorConfig()->getBuildProperty('addHooks')) {
            // save with runtime hools
            $script .= "
            \$ret = true;
            if (\$this->isNew() || \$this->isModified()) {
                \$ret = \$this->preSave(\$con);";
            $this->applyBehaviorModifier('preSave', $script, "			");
            $script .= "
            }";

            $script .= "
            if (\$this->isNew()) {
                \$ret = \$ret && \$this->preInsert(\$con);";
            $this->applyBehaviorModifier('preInsert', $script, "				");
            $script .= "
            } elseif (\$this->isModified()) {
                \$ret = \$ret && \$this->preUpdate(\$con);";
            $this->applyBehaviorModifier('preUpdate', $script, "				");
            $script .= "
            }
            if (\$ret) {
                \$affectedRows = \$this->doSave(\$con".($reloadOnUpdate || $reloadOnInsert ? ", \$skipReload" : "").");";
//            $script .= "
//                if (\$isInsert) {
//                    \$this->postInsert(\$con);";
//            $this->applyBehaviorModifier('postInsert', $script, "					");
//            $script .= "
//                } else {
//                    \$this->postUpdate(\$con);";
//            $this->applyBehaviorModifier('postUpdate', $script, "					");
//            $script .= "
//                }
//                \$this->postSave(\$con);";
//                $this->applyBehaviorModifier('postSave', $script, "				");
                $script .= "
                ".$this->getPeerClassname()."::addInstanceToPool(\$this);
            } else {
                \$affectedRows = 0;
            }
            \$con->commit();

            return \$affectedRows;";
        } else {
            // save without runtime hooks
            $this->applyBehaviorModifier('preSave', $script, "			");
            if ($this->hasBehaviorModifier('preUpdate')) {
                $script .= "
            if (!\$isInsert) {";
                $this->applyBehaviorModifier('preUpdate', $script, "				");
                $script .= "
            }";
            }
            if ($this->hasBehaviorModifier('preInsert')) {
                $script .= "
            if (\$isInsert) {";
                $this->applyBehaviorModifier('preInsert', $script, "				");
                $script .= "
            }";
            }
            $script .= "
            \$affectedRows = \$this->doSave(\$con".($reloadOnUpdate || $reloadOnInsert ? ", \$skipReload" : "").");";
            $this->applyBehaviorModifier('postSave', $script, "			");
            if ($this->hasBehaviorModifier('postUpdate')) {
                $script .= "
            if (!\$isInsert) {";
                $this->applyBehaviorModifier('postUpdate', $script, "				");
                $script .= "
            }";
            }
            if ($this->hasBehaviorModifier('postInsert')) {
                $script .= "
            if (\$isInsert) {";
                $this->applyBehaviorModifier('postInsert', $script, "				");
                $script .= "
            }";
            }
            $script .= "
            \$con->commit();
            ".$this->getPeerClassname()."::addInstanceToPool(\$this);

            return \$affectedRows;";
        }

        $script .= "
        } catch (Exception \$e) {
            \$con->rollBack();
            throw \$e;
        }";
    }

}
