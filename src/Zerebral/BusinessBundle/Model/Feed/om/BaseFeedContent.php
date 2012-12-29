<?php

namespace Zerebral\BusinessBundle\Model\Feed\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Glorpen\PropelEvent\PropelEventBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;
use Zerebral\BusinessBundle\Model\Feed\FeedComment;
use Zerebral\BusinessBundle\Model\Feed\FeedCommentQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedContent;
use Zerebral\BusinessBundle\Model\Feed\FeedContentPeer;
use Zerebral\BusinessBundle\Model\Feed\FeedContentQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\Feed\FeedItemQuery;

abstract class BaseFeedContent extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedContentPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        FeedContentPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinit loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * The value for the text field.
     * @var        string
     */
    protected $text;

    /**
     * The value for the link_url field.
     * @var        string
     */
    protected $link_url;

    /**
     * The value for the link_title field.
     * @var        string
     */
    protected $link_title;

    /**
     * The value for the link_description field.
     * @var        string
     */
    protected $link_description;

    /**
     * The value for the link_thumbnail_url field.
     * @var        string
     */
    protected $link_thumbnail_url;

    /**
     * @var        PropelObjectCollection|FeedItem[] Collection to store aggregation of FeedItem objects.
     */
    protected $collFeedItems;
    protected $collFeedItemsPartial;

    /**
     * @var        PropelObjectCollection|FeedComment[] Collection to store aggregation of FeedComment objects.
     */
    protected $collFeedComments;
    protected $collFeedCommentsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $feedItemsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $feedCommentsScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [type] column value.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the [text] column value.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get the [link_url] column value.
     *
     * @return string
     */
    public function getLinkUrl()
    {
        return $this->link_url;
    }

    /**
     * Get the [link_title] column value.
     *
     * @return string
     */
    public function getLinkTitle()
    {
        return $this->link_title;
    }

    /**
     * Get the [link_description] column value.
     *
     * @return string
     */
    public function getLinkDescription()
    {
        return $this->link_description;
    }

    /**
     * Get the [link_thumbnail_url] column value.
     *
     * @return string
     */
    public function getLinkThumbnailUrl()
    {
        return $this->link_thumbnail_url;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return FeedContent The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = FeedContentPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [type] column.
     *
     * @param string $v new value
     * @return FeedContent The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = FeedContentPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [text] column.
     *
     * @param string $v new value
     * @return FeedContent The current object (for fluent API support)
     */
    public function setText($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->text !== $v) {
            $this->text = $v;
            $this->modifiedColumns[] = FeedContentPeer::TEXT;
        }


        return $this;
    } // setText()

    /**
     * Set the value of [link_url] column.
     *
     * @param string $v new value
     * @return FeedContent The current object (for fluent API support)
     */
    public function setLinkUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->link_url !== $v) {
            $this->link_url = $v;
            $this->modifiedColumns[] = FeedContentPeer::LINK_URL;
        }


        return $this;
    } // setLinkUrl()

    /**
     * Set the value of [link_title] column.
     *
     * @param string $v new value
     * @return FeedContent The current object (for fluent API support)
     */
    public function setLinkTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->link_title !== $v) {
            $this->link_title = $v;
            $this->modifiedColumns[] = FeedContentPeer::LINK_TITLE;
        }


        return $this;
    } // setLinkTitle()

    /**
     * Set the value of [link_description] column.
     *
     * @param string $v new value
     * @return FeedContent The current object (for fluent API support)
     */
    public function setLinkDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->link_description !== $v) {
            $this->link_description = $v;
            $this->modifiedColumns[] = FeedContentPeer::LINK_DESCRIPTION;
        }


        return $this;
    } // setLinkDescription()

    /**
     * Set the value of [link_thumbnail_url] column.
     *
     * @param string $v new value
     * @return FeedContent The current object (for fluent API support)
     */
    public function setLinkThumbnailUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->link_thumbnail_url !== $v) {
            $this->link_thumbnail_url = $v;
            $this->modifiedColumns[] = FeedContentPeer::LINK_THUMBNAIL_URL;
        }


        return $this;
    } // setLinkThumbnailUrl()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->type = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->text = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->link_url = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->link_title = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->link_description = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->link_thumbnail_url = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 7; // 7 = FeedContentPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating FeedContent object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(FeedContentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = FeedContentPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collFeedItems = null;

            $this->collFeedComments = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(FeedContentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = FeedContentQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            // event behavior
            EventDispatcherProxy::trigger(array('delete.pre','model.delete.pre'), new ModelEvent($this));
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                // event behavior
                EventDispatcherProxy::trigger(array('delete.post', 'model.delete.post'), new ModelEvent($this));
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(FeedContentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // event behavior
            EventDispatcherProxy::trigger('model.save.pre', new ModelEvent($this));
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // event behavior
                EventDispatcherProxy::trigger('model.insert.pre', new ModelEvent($this));
            } else {
                $ret = $ret && $this->preUpdate($con);
                // event behavior
                EventDispatcherProxy::trigger(array('update.pre', 'model.update.pre'), new ModelEvent($this));
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                    // event behavior
                    EventDispatcherProxy::trigger('model.insert.post', new ModelEvent($this));
                } else {
                    $this->postUpdate($con);
                    // event behavior
                    EventDispatcherProxy::trigger(array('update.post', 'model.update.post'), new ModelEvent($this));
                }
                $this->postSave($con);
                // event behavior
                EventDispatcherProxy::trigger('model.save.post', new ModelEvent($this));
                FeedContentPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->feedItemsScheduledForDeletion !== null) {
                if (!$this->feedItemsScheduledForDeletion->isEmpty()) {
                    FeedItemQuery::create()
                        ->filterByPrimaryKeys($this->feedItemsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->feedItemsScheduledForDeletion = null;
                }
            }

            if ($this->collFeedItems !== null) {
                foreach ($this->collFeedItems as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->feedCommentsScheduledForDeletion !== null) {
                if (!$this->feedCommentsScheduledForDeletion->isEmpty()) {
                    FeedCommentQuery::create()
                        ->filterByPrimaryKeys($this->feedCommentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->feedCommentsScheduledForDeletion = null;
                }
            }

            if ($this->collFeedComments !== null) {
                foreach ($this->collFeedComments as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = FeedContentPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . FeedContentPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(FeedContentPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(FeedContentPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '`type`';
        }
        if ($this->isColumnModified(FeedContentPeer::TEXT)) {
            $modifiedColumns[':p' . $index++]  = '`text`';
        }
        if ($this->isColumnModified(FeedContentPeer::LINK_URL)) {
            $modifiedColumns[':p' . $index++]  = '`link_url`';
        }
        if ($this->isColumnModified(FeedContentPeer::LINK_TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`link_title`';
        }
        if ($this->isColumnModified(FeedContentPeer::LINK_DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '`link_description`';
        }
        if ($this->isColumnModified(FeedContentPeer::LINK_THUMBNAIL_URL)) {
            $modifiedColumns[':p' . $index++]  = '`link_thumbnail_url`';
        }

        $sql = sprintf(
            'INSERT INTO `feed_contents` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`type`':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case '`text`':
                        $stmt->bindValue($identifier, $this->text, PDO::PARAM_STR);
                        break;
                    case '`link_url`':
                        $stmt->bindValue($identifier, $this->link_url, PDO::PARAM_STR);
                        break;
                    case '`link_title`':
                        $stmt->bindValue($identifier, $this->link_title, PDO::PARAM_STR);
                        break;
                    case '`link_description`':
                        $stmt->bindValue($identifier, $this->link_description, PDO::PARAM_STR);
                        break;
                    case '`link_thumbnail_url`':
                        $stmt->bindValue($identifier, $this->link_thumbnail_url, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = FeedContentPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collFeedItems !== null) {
                    foreach ($this->collFeedItems as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collFeedComments !== null) {
                    foreach ($this->collFeedComments as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = FeedContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getType();
                break;
            case 2:
                return $this->getText();
                break;
            case 3:
                return $this->getLinkUrl();
                break;
            case 4:
                return $this->getLinkTitle();
                break;
            case 5:
                return $this->getLinkDescription();
                break;
            case 6:
                return $this->getLinkThumbnailUrl();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['FeedContent'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['FeedContent'][$this->getPrimaryKey()] = true;
        $keys = FeedContentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getType(),
            $keys[2] => $this->getText(),
            $keys[3] => $this->getLinkUrl(),
            $keys[4] => $this->getLinkTitle(),
            $keys[5] => $this->getLinkDescription(),
            $keys[6] => $this->getLinkThumbnailUrl(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collFeedItems) {
                $result['FeedItems'] = $this->collFeedItems->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFeedComments) {
                $result['FeedComments'] = $this->collFeedComments->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = FeedContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setType($value);
                break;
            case 2:
                $this->setText($value);
                break;
            case 3:
                $this->setLinkUrl($value);
                break;
            case 4:
                $this->setLinkTitle($value);
                break;
            case 5:
                $this->setLinkDescription($value);
                break;
            case 6:
                $this->setLinkThumbnailUrl($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = FeedContentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setType($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setText($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setLinkUrl($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setLinkTitle($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setLinkDescription($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setLinkThumbnailUrl($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(FeedContentPeer::DATABASE_NAME);

        if ($this->isColumnModified(FeedContentPeer::ID)) $criteria->add(FeedContentPeer::ID, $this->id);
        if ($this->isColumnModified(FeedContentPeer::TYPE)) $criteria->add(FeedContentPeer::TYPE, $this->type);
        if ($this->isColumnModified(FeedContentPeer::TEXT)) $criteria->add(FeedContentPeer::TEXT, $this->text);
        if ($this->isColumnModified(FeedContentPeer::LINK_URL)) $criteria->add(FeedContentPeer::LINK_URL, $this->link_url);
        if ($this->isColumnModified(FeedContentPeer::LINK_TITLE)) $criteria->add(FeedContentPeer::LINK_TITLE, $this->link_title);
        if ($this->isColumnModified(FeedContentPeer::LINK_DESCRIPTION)) $criteria->add(FeedContentPeer::LINK_DESCRIPTION, $this->link_description);
        if ($this->isColumnModified(FeedContentPeer::LINK_THUMBNAIL_URL)) $criteria->add(FeedContentPeer::LINK_THUMBNAIL_URL, $this->link_thumbnail_url);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(FeedContentPeer::DATABASE_NAME);
        $criteria->add(FeedContentPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of FeedContent (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setType($this->getType());
        $copyObj->setText($this->getText());
        $copyObj->setLinkUrl($this->getLinkUrl());
        $copyObj->setLinkTitle($this->getLinkTitle());
        $copyObj->setLinkDescription($this->getLinkDescription());
        $copyObj->setLinkThumbnailUrl($this->getLinkThumbnailUrl());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getFeedItems() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFeedItem($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getFeedComments() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFeedComment($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return FeedContent Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return FeedContentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new FeedContentPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('FeedItem' == $relationName) {
            $this->initFeedItems();
        }
        if ('FeedComment' == $relationName) {
            $this->initFeedComments();
        }
    }

    /**
     * Clears out the collFeedItems collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return FeedContent The current object (for fluent API support)
     * @see        addFeedItems()
     */
    public function clearFeedItems()
    {
        $this->collFeedItems = null; // important to set this to null since that means it is uninitialized
        $this->collFeedItemsPartial = null;

        return $this;
    }

    /**
     * reset is the collFeedItems collection loaded partially
     *
     * @return void
     */
    public function resetPartialFeedItems($v = true)
    {
        $this->collFeedItemsPartial = $v;
    }

    /**
     * Initializes the collFeedItems collection.
     *
     * By default this just sets the collFeedItems collection to an empty array (like clearcollFeedItems());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFeedItems($overrideExisting = true)
    {
        if (null !== $this->collFeedItems && !$overrideExisting) {
            return;
        }
        $this->collFeedItems = new PropelObjectCollection();
        $this->collFeedItems->setModel('FeedItem');
    }

    /**
     * Gets an array of FeedItem objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this FeedContent is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|FeedItem[] List of FeedItem objects
     * @throws PropelException
     */
    public function getFeedItems($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collFeedItemsPartial && !$this->isNew();
        if (null === $this->collFeedItems || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFeedItems) {
                // return empty collection
                $this->initFeedItems();
            } else {
                $collFeedItems = FeedItemQuery::create(null, $criteria)
                    ->filterByFeedContent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collFeedItemsPartial && count($collFeedItems)) {
                      $this->initFeedItems(false);

                      foreach($collFeedItems as $obj) {
                        if (false == $this->collFeedItems->contains($obj)) {
                          $this->collFeedItems->append($obj);
                        }
                      }

                      $this->collFeedItemsPartial = true;
                    }

                    return $collFeedItems;
                }

                if($partial && $this->collFeedItems) {
                    foreach($this->collFeedItems as $obj) {
                        if($obj->isNew()) {
                            $collFeedItems[] = $obj;
                        }
                    }
                }

                $this->collFeedItems = $collFeedItems;
                $this->collFeedItemsPartial = false;
            }
        }

        return $this->collFeedItems;
    }

    /**
     * Sets a collection of FeedItem objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $feedItems A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return FeedContent The current object (for fluent API support)
     */
    public function setFeedItems(PropelCollection $feedItems, PropelPDO $con = null)
    {
        $feedItemsToDelete = $this->getFeedItems(new Criteria(), $con)->diff($feedItems);

        $this->feedItemsScheduledForDeletion = unserialize(serialize($feedItemsToDelete));

        foreach ($feedItemsToDelete as $feedItemRemoved) {
            $feedItemRemoved->setFeedContent(null);
        }

        $this->collFeedItems = null;
        foreach ($feedItems as $feedItem) {
            $this->addFeedItem($feedItem);
        }

        $this->collFeedItems = $feedItems;
        $this->collFeedItemsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FeedItem objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related FeedItem objects.
     * @throws PropelException
     */
    public function countFeedItems(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collFeedItemsPartial && !$this->isNew();
        if (null === $this->collFeedItems || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFeedItems) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getFeedItems());
            }
            $query = FeedItemQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFeedContent($this)
                ->count($con);
        }

        return count($this->collFeedItems);
    }

    /**
     * Method called to associate a FeedItem object to this object
     * through the FeedItem foreign key attribute.
     *
     * @param    FeedItem $l FeedItem
     * @return FeedContent The current object (for fluent API support)
     */
    public function addFeedItem(FeedItem $l)
    {
        if ($this->collFeedItems === null) {
            $this->initFeedItems();
            $this->collFeedItemsPartial = true;
        }
        if (!in_array($l, $this->collFeedItems->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddFeedItem($l);
        }

        return $this;
    }

    /**
     * @param	FeedItem $feedItem The feedItem object to add.
     */
    protected function doAddFeedItem($feedItem)
    {
        $this->collFeedItems[]= $feedItem;
        $feedItem->setFeedContent($this);
    }

    /**
     * @param	FeedItem $feedItem The feedItem object to remove.
     * @return FeedContent The current object (for fluent API support)
     */
    public function removeFeedItem($feedItem)
    {
        if ($this->getFeedItems()->contains($feedItem)) {
            $this->collFeedItems->remove($this->collFeedItems->search($feedItem));
            if (null === $this->feedItemsScheduledForDeletion) {
                $this->feedItemsScheduledForDeletion = clone $this->collFeedItems;
                $this->feedItemsScheduledForDeletion->clear();
            }
            $this->feedItemsScheduledForDeletion[]= clone $feedItem;
            $feedItem->setFeedContent(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this FeedContent is new, it will return
     * an empty collection; or if this FeedContent has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in FeedContent.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FeedItem[] List of FeedItem objects
     */
    public function getFeedItemsJoinAssignment($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FeedItemQuery::create(null, $criteria);
        $query->joinWith('Assignment', $join_behavior);

        return $this->getFeedItems($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this FeedContent is new, it will return
     * an empty collection; or if this FeedContent has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in FeedContent.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FeedItem[] List of FeedItem objects
     */
    public function getFeedItemsJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FeedItemQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getFeedItems($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this FeedContent is new, it will return
     * an empty collection; or if this FeedContent has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in FeedContent.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FeedItem[] List of FeedItem objects
     */
    public function getFeedItemsJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FeedItemQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getFeedItems($query, $con);
    }

    /**
     * Clears out the collFeedComments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return FeedContent The current object (for fluent API support)
     * @see        addFeedComments()
     */
    public function clearFeedComments()
    {
        $this->collFeedComments = null; // important to set this to null since that means it is uninitialized
        $this->collFeedCommentsPartial = null;

        return $this;
    }

    /**
     * reset is the collFeedComments collection loaded partially
     *
     * @return void
     */
    public function resetPartialFeedComments($v = true)
    {
        $this->collFeedCommentsPartial = $v;
    }

    /**
     * Initializes the collFeedComments collection.
     *
     * By default this just sets the collFeedComments collection to an empty array (like clearcollFeedComments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFeedComments($overrideExisting = true)
    {
        if (null !== $this->collFeedComments && !$overrideExisting) {
            return;
        }
        $this->collFeedComments = new PropelObjectCollection();
        $this->collFeedComments->setModel('FeedComment');
    }

    /**
     * Gets an array of FeedComment objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this FeedContent is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|FeedComment[] List of FeedComment objects
     * @throws PropelException
     */
    public function getFeedComments($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collFeedCommentsPartial && !$this->isNew();
        if (null === $this->collFeedComments || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFeedComments) {
                // return empty collection
                $this->initFeedComments();
            } else {
                $collFeedComments = FeedCommentQuery::create(null, $criteria)
                    ->filterByFeedContent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collFeedCommentsPartial && count($collFeedComments)) {
                      $this->initFeedComments(false);

                      foreach($collFeedComments as $obj) {
                        if (false == $this->collFeedComments->contains($obj)) {
                          $this->collFeedComments->append($obj);
                        }
                      }

                      $this->collFeedCommentsPartial = true;
                    }

                    return $collFeedComments;
                }

                if($partial && $this->collFeedComments) {
                    foreach($this->collFeedComments as $obj) {
                        if($obj->isNew()) {
                            $collFeedComments[] = $obj;
                        }
                    }
                }

                $this->collFeedComments = $collFeedComments;
                $this->collFeedCommentsPartial = false;
            }
        }

        return $this->collFeedComments;
    }

    /**
     * Sets a collection of FeedComment objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $feedComments A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return FeedContent The current object (for fluent API support)
     */
    public function setFeedComments(PropelCollection $feedComments, PropelPDO $con = null)
    {
        $feedCommentsToDelete = $this->getFeedComments(new Criteria(), $con)->diff($feedComments);

        $this->feedCommentsScheduledForDeletion = unserialize(serialize($feedCommentsToDelete));

        foreach ($feedCommentsToDelete as $feedCommentRemoved) {
            $feedCommentRemoved->setFeedContent(null);
        }

        $this->collFeedComments = null;
        foreach ($feedComments as $feedComment) {
            $this->addFeedComment($feedComment);
        }

        $this->collFeedComments = $feedComments;
        $this->collFeedCommentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FeedComment objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related FeedComment objects.
     * @throws PropelException
     */
    public function countFeedComments(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collFeedCommentsPartial && !$this->isNew();
        if (null === $this->collFeedComments || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFeedComments) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getFeedComments());
            }
            $query = FeedCommentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFeedContent($this)
                ->count($con);
        }

        return count($this->collFeedComments);
    }

    /**
     * Method called to associate a FeedComment object to this object
     * through the FeedComment foreign key attribute.
     *
     * @param    FeedComment $l FeedComment
     * @return FeedContent The current object (for fluent API support)
     */
    public function addFeedComment(FeedComment $l)
    {
        if ($this->collFeedComments === null) {
            $this->initFeedComments();
            $this->collFeedCommentsPartial = true;
        }
        if (!in_array($l, $this->collFeedComments->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddFeedComment($l);
        }

        return $this;
    }

    /**
     * @param	FeedComment $feedComment The feedComment object to add.
     */
    protected function doAddFeedComment($feedComment)
    {
        $this->collFeedComments[]= $feedComment;
        $feedComment->setFeedContent($this);
    }

    /**
     * @param	FeedComment $feedComment The feedComment object to remove.
     * @return FeedContent The current object (for fluent API support)
     */
    public function removeFeedComment($feedComment)
    {
        if ($this->getFeedComments()->contains($feedComment)) {
            $this->collFeedComments->remove($this->collFeedComments->search($feedComment));
            if (null === $this->feedCommentsScheduledForDeletion) {
                $this->feedCommentsScheduledForDeletion = clone $this->collFeedComments;
                $this->feedCommentsScheduledForDeletion->clear();
            }
            $this->feedCommentsScheduledForDeletion[]= clone $feedComment;
            $feedComment->setFeedContent(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this FeedContent is new, it will return
     * an empty collection; or if this FeedContent has previously
     * been saved, it will retrieve related FeedComments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in FeedContent.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FeedComment[] List of FeedComment objects
     */
    public function getFeedCommentsJoinFeedItem($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FeedCommentQuery::create(null, $criteria);
        $query->joinWith('FeedItem', $join_behavior);

        return $this->getFeedComments($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this FeedContent is new, it will return
     * an empty collection; or if this FeedContent has previously
     * been saved, it will retrieve related FeedComments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in FeedContent.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FeedComment[] List of FeedComment objects
     */
    public function getFeedCommentsJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FeedCommentQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getFeedComments($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->type = null;
        $this->text = null;
        $this->link_url = null;
        $this->link_title = null;
        $this->link_description = null;
        $this->link_thumbnail_url = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volumne/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collFeedItems) {
                foreach ($this->collFeedItems as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFeedComments) {
                foreach ($this->collFeedComments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collFeedItems instanceof PropelCollection) {
            $this->collFeedItems->clearIterator();
        }
        $this->collFeedItems = null;
        if ($this->collFeedComments instanceof PropelCollection) {
            $this->collFeedComments->clearIterator();
        }
        $this->collFeedComments = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(FeedContentPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
