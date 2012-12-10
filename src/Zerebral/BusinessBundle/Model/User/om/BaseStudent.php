<?php

namespace Zerebral\BusinessBundle\Model\User\om;

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
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignment;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;
use Zerebral\BusinessBundle\Model\Course\CourseStudent;
use Zerebral\BusinessBundle\Model\Course\CourseStudentQuery;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\StudentPeer;
use Zerebral\BusinessBundle\Model\User\StudentQuery;
use Zerebral\BusinessBundle\Model\User\User;
use Zerebral\BusinessBundle\Model\User\UserQuery;

abstract class BaseStudent extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Zerebral\\BusinessBundle\\Model\\User\\StudentPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        StudentPeer
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
     * The value for the user_id field.
     * @var        int
     */
    protected $user_id;

    /**
     * The value for the bio field.
     * @var        string
     */
    protected $bio;

    /**
     * The value for the activities field.
     * @var        string
     */
    protected $activities;

    /**
     * The value for the interests field.
     * @var        string
     */
    protected $interests;

    /**
     * @var        User
     */
    protected $aUser;

    /**
     * @var        PropelObjectCollection|StudentAssignment[] Collection to store aggregation of StudentAssignment objects.
     */
    protected $collStudentAssignments;
    protected $collStudentAssignmentsPartial;

    /**
     * @var        PropelObjectCollection|CourseStudent[] Collection to store aggregation of CourseStudent objects.
     */
    protected $collCourseStudents;
    protected $collCourseStudentsPartial;

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
    protected $studentAssignmentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseStudentsScheduledForDeletion = null;

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
     * Get the [user_id] column value.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Get the [bio] column value.
     *
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Get the [activities] column value.
     *
     * @return string
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Get the [interests] column value.
     *
     * @return string
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return Student The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = StudentPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [user_id] column.
     *
     * @param int $v new value
     * @return Student The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = StudentPeer::USER_ID;
        }

        if ($this->aUser !== null && $this->aUser->getId() !== $v) {
            $this->aUser = null;
        }


        return $this;
    } // setUserId()

    /**
     * Set the value of [bio] column.
     *
     * @param string $v new value
     * @return Student The current object (for fluent API support)
     */
    public function setBio($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->bio !== $v) {
            $this->bio = $v;
            $this->modifiedColumns[] = StudentPeer::BIO;
        }


        return $this;
    } // setBio()

    /**
     * Set the value of [activities] column.
     *
     * @param string $v new value
     * @return Student The current object (for fluent API support)
     */
    public function setActivities($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->activities !== $v) {
            $this->activities = $v;
            $this->modifiedColumns[] = StudentPeer::ACTIVITIES;
        }


        return $this;
    } // setActivities()

    /**
     * Set the value of [interests] column.
     *
     * @param string $v new value
     * @return Student The current object (for fluent API support)
     */
    public function setInterests($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->interests !== $v) {
            $this->interests = $v;
            $this->modifiedColumns[] = StudentPeer::INTERESTS;
        }


        return $this;
    } // setInterests()

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
            $this->user_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->bio = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->activities = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->interests = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 5; // 5 = StudentPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Student object", $e);
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

        if ($this->aUser !== null && $this->user_id !== $this->aUser->getId()) {
            $this->aUser = null;
        }
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
            $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = StudentPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUser = null;
            $this->collStudentAssignments = null;

            $this->collCourseStudents = null;

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
            $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = StudentQuery::create()
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
            $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                StudentPeer::addInstanceToPool($this);
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

            // We call the save method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aUser !== null) {
                if ($this->aUser->isModified() || $this->aUser->isNew()) {
                    $affectedRows += $this->aUser->save($con);
                }
                $this->setUser($this->aUser);
            }

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

            if ($this->studentAssignmentsScheduledForDeletion !== null) {
                if (!$this->studentAssignmentsScheduledForDeletion->isEmpty()) {
                    StudentAssignmentQuery::create()
                        ->filterByPrimaryKeys($this->studentAssignmentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->studentAssignmentsScheduledForDeletion = null;
                }
            }

            if ($this->collStudentAssignments !== null) {
                foreach ($this->collStudentAssignments as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->courseStudentsScheduledForDeletion !== null) {
                if (!$this->courseStudentsScheduledForDeletion->isEmpty()) {
                    CourseStudentQuery::create()
                        ->filterByPrimaryKeys($this->courseStudentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->courseStudentsScheduledForDeletion = null;
                }
            }

            if ($this->collCourseStudents !== null) {
                foreach ($this->collCourseStudents as $referrerFK) {
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

        $this->modifiedColumns[] = StudentPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . StudentPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(StudentPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(StudentPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(StudentPeer::BIO)) {
            $modifiedColumns[':p' . $index++]  = '`bio`';
        }
        if ($this->isColumnModified(StudentPeer::ACTIVITIES)) {
            $modifiedColumns[':p' . $index++]  = '`activities`';
        }
        if ($this->isColumnModified(StudentPeer::INTERESTS)) {
            $modifiedColumns[':p' . $index++]  = '`interests`';
        }

        $sql = sprintf(
            'INSERT INTO `students` (%s) VALUES (%s)',
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
                    case '`user_id`':
                        $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
                        break;
                    case '`bio`':
                        $stmt->bindValue($identifier, $this->bio, PDO::PARAM_STR);
                        break;
                    case '`activities`':
                        $stmt->bindValue($identifier, $this->activities, PDO::PARAM_STR);
                        break;
                    case '`interests`':
                        $stmt->bindValue($identifier, $this->interests, PDO::PARAM_STR);
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


            // We call the validate method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aUser !== null) {
                if (!$this->aUser->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aUser->getValidationFailures());
                }
            }


            if (($retval = StudentPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collStudentAssignments !== null) {
                    foreach ($this->collStudentAssignments as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCourseStudents !== null) {
                    foreach ($this->collCourseStudents as $referrerFK) {
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
        $pos = StudentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getUserId();
                break;
            case 2:
                return $this->getBio();
                break;
            case 3:
                return $this->getActivities();
                break;
            case 4:
                return $this->getInterests();
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
        if (isset($alreadyDumpedObjects['Student'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Student'][$this->getPrimaryKey()] = true;
        $keys = StudentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getBio(),
            $keys[3] => $this->getActivities(),
            $keys[4] => $this->getInterests(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aUser) {
                $result['User'] = $this->aUser->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collStudentAssignments) {
                $result['StudentAssignments'] = $this->collStudentAssignments->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseStudents) {
                $result['CourseStudents'] = $this->collCourseStudents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = StudentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setUserId($value);
                break;
            case 2:
                $this->setBio($value);
                break;
            case 3:
                $this->setActivities($value);
                break;
            case 4:
                $this->setInterests($value);
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
        $keys = StudentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setBio($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setActivities($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setInterests($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(StudentPeer::DATABASE_NAME);

        if ($this->isColumnModified(StudentPeer::ID)) $criteria->add(StudentPeer::ID, $this->id);
        if ($this->isColumnModified(StudentPeer::USER_ID)) $criteria->add(StudentPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(StudentPeer::BIO)) $criteria->add(StudentPeer::BIO, $this->bio);
        if ($this->isColumnModified(StudentPeer::ACTIVITIES)) $criteria->add(StudentPeer::ACTIVITIES, $this->activities);
        if ($this->isColumnModified(StudentPeer::INTERESTS)) $criteria->add(StudentPeer::INTERESTS, $this->interests);

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
        $criteria = new Criteria(StudentPeer::DATABASE_NAME);
        $criteria->add(StudentPeer::ID, $this->id);

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
     * @param object $copyObj An object of Student (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setBio($this->getBio());
        $copyObj->setActivities($this->getActivities());
        $copyObj->setInterests($this->getInterests());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getStudentAssignments() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addStudentAssignment($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCourseStudents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseStudent($relObj->copy($deepCopy));
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
     * @return Student Clone of current object.
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
     * @return StudentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new StudentPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param             User $v
     * @return Student The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUser(User $v = null)
    {
        if ($v === null) {
            $this->setUserId(NULL);
        } else {
            $this->setUserId($v->getId());
        }

        $this->aUser = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the User object, it will not be re-added.
        if ($v !== null) {
            $v->addStudent($this);
        }


        return $this;
    }


    /**
     * Get the associated User object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return User The associated User object.
     * @throws PropelException
     */
    public function getUser(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aUser === null && ($this->user_id !== null) && $doQuery) {
            $this->aUser = UserQuery::create()->findPk($this->user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUser->addStudents($this);
             */
        }

        return $this->aUser;
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
        if ('StudentAssignment' == $relationName) {
            $this->initStudentAssignments();
        }
        if ('CourseStudent' == $relationName) {
            $this->initCourseStudents();
        }
    }

    /**
     * Clears out the collStudentAssignments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Student The current object (for fluent API support)
     * @see        addStudentAssignments()
     */
    public function clearStudentAssignments()
    {
        $this->collStudentAssignments = null; // important to set this to null since that means it is uninitialized
        $this->collStudentAssignmentsPartial = null;

        return $this;
    }

    /**
     * reset is the collStudentAssignments collection loaded partially
     *
     * @return void
     */
    public function resetPartialStudentAssignments($v = true)
    {
        $this->collStudentAssignmentsPartial = $v;
    }

    /**
     * Initializes the collStudentAssignments collection.
     *
     * By default this just sets the collStudentAssignments collection to an empty array (like clearcollStudentAssignments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initStudentAssignments($overrideExisting = true)
    {
        if (null !== $this->collStudentAssignments && !$overrideExisting) {
            return;
        }
        $this->collStudentAssignments = new PropelObjectCollection();
        $this->collStudentAssignments->setModel('StudentAssignment');
    }

    /**
     * Gets an array of StudentAssignment objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Student is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|StudentAssignment[] List of StudentAssignment objects
     * @throws PropelException
     */
    public function getStudentAssignments($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collStudentAssignmentsPartial && !$this->isNew();
        if (null === $this->collStudentAssignments || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collStudentAssignments) {
                // return empty collection
                $this->initStudentAssignments();
            } else {
                $collStudentAssignments = StudentAssignmentQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collStudentAssignmentsPartial && count($collStudentAssignments)) {
                      $this->initStudentAssignments(false);

                      foreach($collStudentAssignments as $obj) {
                        if (false == $this->collStudentAssignments->contains($obj)) {
                          $this->collStudentAssignments->append($obj);
                        }
                      }

                      $this->collStudentAssignmentsPartial = true;
                    }

                    return $collStudentAssignments;
                }

                if($partial && $this->collStudentAssignments) {
                    foreach($this->collStudentAssignments as $obj) {
                        if($obj->isNew()) {
                            $collStudentAssignments[] = $obj;
                        }
                    }
                }

                $this->collStudentAssignments = $collStudentAssignments;
                $this->collStudentAssignmentsPartial = false;
            }
        }

        return $this->collStudentAssignments;
    }

    /**
     * Sets a collection of StudentAssignment objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $studentAssignments A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Student The current object (for fluent API support)
     */
    public function setStudentAssignments(PropelCollection $studentAssignments, PropelPDO $con = null)
    {
        $this->studentAssignmentsScheduledForDeletion = $this->getStudentAssignments(new Criteria(), $con)->diff($studentAssignments);

        foreach ($this->studentAssignmentsScheduledForDeletion as $studentAssignmentRemoved) {
            $studentAssignmentRemoved->setStudent(null);
        }

        $this->collStudentAssignments = null;
        foreach ($studentAssignments as $studentAssignment) {
            $this->addStudentAssignment($studentAssignment);
        }

        $this->collStudentAssignments = $studentAssignments;
        $this->collStudentAssignmentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related StudentAssignment objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related StudentAssignment objects.
     * @throws PropelException
     */
    public function countStudentAssignments(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collStudentAssignmentsPartial && !$this->isNew();
        if (null === $this->collStudentAssignments || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collStudentAssignments) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getStudentAssignments());
            }
            $query = StudentAssignmentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByStudent($this)
                ->count($con);
        }

        return count($this->collStudentAssignments);
    }

    /**
     * Method called to associate a StudentAssignment object to this object
     * through the StudentAssignment foreign key attribute.
     *
     * @param    StudentAssignment $l StudentAssignment
     * @return Student The current object (for fluent API support)
     */
    public function addStudentAssignment(StudentAssignment $l)
    {
        if ($this->collStudentAssignments === null) {
            $this->initStudentAssignments();
            $this->collStudentAssignmentsPartial = true;
        }
        if (!in_array($l, $this->collStudentAssignments->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddStudentAssignment($l);
        }

        return $this;
    }

    /**
     * @param	StudentAssignment $studentAssignment The studentAssignment object to add.
     */
    protected function doAddStudentAssignment($studentAssignment)
    {
        $this->collStudentAssignments[]= $studentAssignment;
        $studentAssignment->setStudent($this);
    }

    /**
     * @param	StudentAssignment $studentAssignment The studentAssignment object to remove.
     * @return Student The current object (for fluent API support)
     */
    public function removeStudentAssignment($studentAssignment)
    {
        if ($this->getStudentAssignments()->contains($studentAssignment)) {
            $this->collStudentAssignments->remove($this->collStudentAssignments->search($studentAssignment));
            if (null === $this->studentAssignmentsScheduledForDeletion) {
                $this->studentAssignmentsScheduledForDeletion = clone $this->collStudentAssignments;
                $this->studentAssignmentsScheduledForDeletion->clear();
            }
            $this->studentAssignmentsScheduledForDeletion[]= $studentAssignment;
            $studentAssignment->setStudent(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Student is new, it will return
     * an empty collection; or if this Student has previously
     * been saved, it will retrieve related StudentAssignments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Student.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|StudentAssignment[] List of StudentAssignment objects
     */
    public function getStudentAssignmentsJoinAssignment($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = StudentAssignmentQuery::create(null, $criteria);
        $query->joinWith('Assignment', $join_behavior);

        return $this->getStudentAssignments($query, $con);
    }

    /**
     * Clears out the collCourseStudents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Student The current object (for fluent API support)
     * @see        addCourseStudents()
     */
    public function clearCourseStudents()
    {
        $this->collCourseStudents = null; // important to set this to null since that means it is uninitialized
        $this->collCourseStudentsPartial = null;

        return $this;
    }

    /**
     * reset is the collCourseStudents collection loaded partially
     *
     * @return void
     */
    public function resetPartialCourseStudents($v = true)
    {
        $this->collCourseStudentsPartial = $v;
    }

    /**
     * Initializes the collCourseStudents collection.
     *
     * By default this just sets the collCourseStudents collection to an empty array (like clearcollCourseStudents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCourseStudents($overrideExisting = true)
    {
        if (null !== $this->collCourseStudents && !$overrideExisting) {
            return;
        }
        $this->collCourseStudents = new PropelObjectCollection();
        $this->collCourseStudents->setModel('CourseStudent');
    }

    /**
     * Gets an array of CourseStudent objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Student is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CourseStudent[] List of CourseStudent objects
     * @throws PropelException
     */
    public function getCourseStudents($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCourseStudentsPartial && !$this->isNew();
        if (null === $this->collCourseStudents || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCourseStudents) {
                // return empty collection
                $this->initCourseStudents();
            } else {
                $collCourseStudents = CourseStudentQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCourseStudentsPartial && count($collCourseStudents)) {
                      $this->initCourseStudents(false);

                      foreach($collCourseStudents as $obj) {
                        if (false == $this->collCourseStudents->contains($obj)) {
                          $this->collCourseStudents->append($obj);
                        }
                      }

                      $this->collCourseStudentsPartial = true;
                    }

                    return $collCourseStudents;
                }

                if($partial && $this->collCourseStudents) {
                    foreach($this->collCourseStudents as $obj) {
                        if($obj->isNew()) {
                            $collCourseStudents[] = $obj;
                        }
                    }
                }

                $this->collCourseStudents = $collCourseStudents;
                $this->collCourseStudentsPartial = false;
            }
        }

        return $this->collCourseStudents;
    }

    /**
     * Sets a collection of CourseStudent objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $courseStudents A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Student The current object (for fluent API support)
     */
    public function setCourseStudents(PropelCollection $courseStudents, PropelPDO $con = null)
    {
        $this->courseStudentsScheduledForDeletion = $this->getCourseStudents(new Criteria(), $con)->diff($courseStudents);

        foreach ($this->courseStudentsScheduledForDeletion as $courseStudentRemoved) {
            $courseStudentRemoved->setStudent(null);
        }

        $this->collCourseStudents = null;
        foreach ($courseStudents as $courseStudent) {
            $this->addCourseStudent($courseStudent);
        }

        $this->collCourseStudents = $courseStudents;
        $this->collCourseStudentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CourseStudent objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CourseStudent objects.
     * @throws PropelException
     */
    public function countCourseStudents(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCourseStudentsPartial && !$this->isNew();
        if (null === $this->collCourseStudents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCourseStudents) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getCourseStudents());
            }
            $query = CourseStudentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByStudent($this)
                ->count($con);
        }

        return count($this->collCourseStudents);
    }

    /**
     * Method called to associate a CourseStudent object to this object
     * through the CourseStudent foreign key attribute.
     *
     * @param    CourseStudent $l CourseStudent
     * @return Student The current object (for fluent API support)
     */
    public function addCourseStudent(CourseStudent $l)
    {
        if ($this->collCourseStudents === null) {
            $this->initCourseStudents();
            $this->collCourseStudentsPartial = true;
        }
        if (!in_array($l, $this->collCourseStudents->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCourseStudent($l);
        }

        return $this;
    }

    /**
     * @param	CourseStudent $courseStudent The courseStudent object to add.
     */
    protected function doAddCourseStudent($courseStudent)
    {
        $this->collCourseStudents[]= $courseStudent;
        $courseStudent->setStudent($this);
    }

    /**
     * @param	CourseStudent $courseStudent The courseStudent object to remove.
     * @return Student The current object (for fluent API support)
     */
    public function removeCourseStudent($courseStudent)
    {
        if ($this->getCourseStudents()->contains($courseStudent)) {
            $this->collCourseStudents->remove($this->collCourseStudents->search($courseStudent));
            if (null === $this->courseStudentsScheduledForDeletion) {
                $this->courseStudentsScheduledForDeletion = clone $this->collCourseStudents;
                $this->courseStudentsScheduledForDeletion->clear();
            }
            $this->courseStudentsScheduledForDeletion[]= $courseStudent;
            $courseStudent->setStudent(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Student is new, it will return
     * an empty collection; or if this Student has previously
     * been saved, it will retrieve related CourseStudents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Student.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CourseStudent[] List of CourseStudent objects
     */
    public function getCourseStudentsJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseStudentQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getCourseStudents($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->user_id = null;
        $this->bio = null;
        $this->activities = null;
        $this->interests = null;
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
            if ($this->collStudentAssignments) {
                foreach ($this->collStudentAssignments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourseStudents) {
                foreach ($this->collCourseStudents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collStudentAssignments instanceof PropelCollection) {
            $this->collStudentAssignments->clearIterator();
        }
        $this->collStudentAssignments = null;
        if ($this->collCourseStudents instanceof PropelCollection) {
            $this->collCourseStudents->clearIterator();
        }
        $this->collCourseStudents = null;
        $this->aUser = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(StudentPeer::DEFAULT_STRING_FORMAT);
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

    /**
     * Catches calls to virtual methods
     */
    public function __call($name, $params)
    {

        // delegate behavior

        if (is_callable(array('Zerebral\BusinessBundle\Model\User\User', $name))) {
            if (!$delegate = $this->getUser()) {
                $delegate = new User();
                $this->setUser($delegate);
            }

            return call_user_func_array(array($delegate, $name), $params);
        }

        return parent::__call($name, $params);
    }

}
