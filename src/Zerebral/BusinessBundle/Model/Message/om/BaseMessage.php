<?php

namespace Zerebral\BusinessBundle\Model\Message\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Glorpen\PropelEvent\PropelEventBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;
use Zerebral\BusinessBundle\Model\Assignment\Assignment;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignment;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;
use Zerebral\BusinessBundle\Model\File\File;
use Zerebral\BusinessBundle\Model\File\FileQuery;
use Zerebral\BusinessBundle\Model\File\FileReferences;
use Zerebral\BusinessBundle\Model\File\FileReferencesQuery;
use Zerebral\BusinessBundle\Model\Message\Message;
use Zerebral\BusinessBundle\Model\Message\MessagePeer;
use Zerebral\BusinessBundle\Model\Message\MessageQuery;
use Zerebral\BusinessBundle\Model\User\User;
use Zerebral\BusinessBundle\Model\User\UserQuery;

abstract class BaseMessage extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Zerebral\\BusinessBundle\\Model\\Message\\MessagePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        MessagePeer
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
     * The value for the thread_id field.
     * @var        string
     */
    protected $thread_id;

    /**
     * The value for the from_id field.
     * @var        int
     */
    protected $from_id;

    /**
     * The value for the to_id field.
     * @var        int
     */
    protected $to_id;

    /**
     * The value for the is_read field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_read;

    /**
     * The value for the user_id field.
     * @var        int
     */
    protected $user_id;

    /**
     * The value for the subject field.
     * @var        string
     */
    protected $subject;

    /**
     * The value for the body field.
     * @var        string
     */
    protected $body;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * @var        User
     */
    protected $aUserRelatedByUserId;

    /**
     * @var        User
     */
    protected $aUserRelatedByFromId;

    /**
     * @var        User
     */
    protected $aUserRelatedByToId;

    /**
     * @var        PropelObjectCollection|FileReferences[] Collection to store aggregation of FileReferences objects.
     */
    protected $collFileReferencess;
    protected $collFileReferencessPartial;

    /**
     * @var        PropelObjectCollection|File[] Collection to store aggregation of File objects.
     */
    protected $collFiles;

    /**
     * @var        PropelObjectCollection|Assignment[] Collection to store aggregation of Assignment objects.
     */
    protected $collassignmentReferenceIds;

    /**
     * @var        PropelObjectCollection|StudentAssignment[] Collection to store aggregation of StudentAssignment objects.
     */
    protected $collstudentAssignmentReferenceIds;

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
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $filesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $assignmentReferenceIdsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $studentAssignmentReferenceIdsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $fileReferencessScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->is_read = false;
    }

    /**
     * Initializes internal state of BaseMessage object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
        EventDispatcherProxy::trigger(array('construct','model.construct'), new ModelEvent($this));
}

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
     * Get the [thread_id] column value.
     *
     * @return string
     */
    public function getThreadId()
    {
        return $this->thread_id;
    }

    /**
     * Get the [from_id] column value.
     *
     * @return int
     */
    public function getFromId()
    {
        return $this->from_id;
    }

    /**
     * Get the [to_id] column value.
     *
     * @return int
     */
    public function getToId()
    {
        return $this->to_id;
    }

    /**
     * Get the [is_read] column value.
     *
     * @return boolean
     */
    public function getIsRead()
    {
        return $this->is_read;
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
     * Get the [subject] column value.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get the [body] column value.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = null)
    {
        if ($this->created_at === null) {
            return null;
        }

        if ($this->created_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->created_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return Message The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = MessagePeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [thread_id] column.
     *
     * @param string $v new value
     * @return Message The current object (for fluent API support)
     */
    public function setThreadId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->thread_id !== $v) {
            $this->thread_id = $v;
            $this->modifiedColumns[] = MessagePeer::THREAD_ID;
        }


        return $this;
    } // setThreadId()

    /**
     * Set the value of [from_id] column.
     *
     * @param int $v new value
     * @return Message The current object (for fluent API support)
     */
    public function setFromId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->from_id !== $v) {
            $this->from_id = $v;
            $this->modifiedColumns[] = MessagePeer::FROM_ID;
        }

        if ($this->aUserRelatedByFromId !== null && $this->aUserRelatedByFromId->getId() !== $v) {
            $this->aUserRelatedByFromId = null;
        }


        return $this;
    } // setFromId()

    /**
     * Set the value of [to_id] column.
     *
     * @param int $v new value
     * @return Message The current object (for fluent API support)
     */
    public function setToId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->to_id !== $v) {
            $this->to_id = $v;
            $this->modifiedColumns[] = MessagePeer::TO_ID;
        }

        if ($this->aUserRelatedByToId !== null && $this->aUserRelatedByToId->getId() !== $v) {
            $this->aUserRelatedByToId = null;
        }


        return $this;
    } // setToId()

    /**
     * Sets the value of the [is_read] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Message The current object (for fluent API support)
     */
    public function setIsRead($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_read !== $v) {
            $this->is_read = $v;
            $this->modifiedColumns[] = MessagePeer::IS_READ;
        }


        return $this;
    } // setIsRead()

    /**
     * Set the value of [user_id] column.
     *
     * @param int $v new value
     * @return Message The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = MessagePeer::USER_ID;
        }

        if ($this->aUserRelatedByUserId !== null && $this->aUserRelatedByUserId->getId() !== $v) {
            $this->aUserRelatedByUserId = null;
        }


        return $this;
    } // setUserId()

    /**
     * Set the value of [subject] column.
     *
     * @param string $v new value
     * @return Message The current object (for fluent API support)
     */
    public function setSubject($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->subject !== $v) {
            $this->subject = $v;
            $this->modifiedColumns[] = MessagePeer::SUBJECT;
        }


        return $this;
    } // setSubject()

    /**
     * Set the value of [body] column.
     *
     * @param string $v new value
     * @return Message The current object (for fluent API support)
     */
    public function setBody($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->body !== $v) {
            $this->body = $v;
            $this->modifiedColumns[] = MessagePeer::BODY;
        }


        return $this;
    } // setBody()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Message The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = MessagePeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

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
            if ($this->is_read !== false) {
                return false;
            }

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
            $this->thread_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->from_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->to_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->is_read = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
            $this->user_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->subject = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->body = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 9; // 9 = MessagePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Message object", $e);
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

        if ($this->aUserRelatedByFromId !== null && $this->from_id !== $this->aUserRelatedByFromId->getId()) {
            $this->aUserRelatedByFromId = null;
        }
        if ($this->aUserRelatedByToId !== null && $this->to_id !== $this->aUserRelatedByToId->getId()) {
            $this->aUserRelatedByToId = null;
        }
        if ($this->aUserRelatedByUserId !== null && $this->user_id !== $this->aUserRelatedByUserId->getId()) {
            $this->aUserRelatedByUserId = null;
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
            $con = Propel::getConnection(MessagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = MessagePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUserRelatedByUserId = null;
            $this->aUserRelatedByFromId = null;
            $this->aUserRelatedByToId = null;
            $this->collFileReferencess = null;

            $this->collFiles = null;
            $this->collassignmentReferenceIds = null;
            $this->collstudentAssignmentReferenceIds = null;
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
            $con = Propel::getConnection(MessagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = MessageQuery::create()
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
            $con = Propel::getConnection(MessagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                MessagePeer::addInstanceToPool($this);
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

            if ($this->aUserRelatedByUserId !== null) {
                if ($this->aUserRelatedByUserId->isModified() || $this->aUserRelatedByUserId->isNew()) {
                    $affectedRows += $this->aUserRelatedByUserId->save($con);
                }
                $this->setUserRelatedByUserId($this->aUserRelatedByUserId);
            }

            if ($this->aUserRelatedByFromId !== null) {
                if ($this->aUserRelatedByFromId->isModified() || $this->aUserRelatedByFromId->isNew()) {
                    $affectedRows += $this->aUserRelatedByFromId->save($con);
                }
                $this->setUserRelatedByFromId($this->aUserRelatedByFromId);
            }

            if ($this->aUserRelatedByToId !== null) {
                if ($this->aUserRelatedByToId->isModified() || $this->aUserRelatedByToId->isNew()) {
                    $affectedRows += $this->aUserRelatedByToId->save($con);
                }
                $this->setUserRelatedByToId($this->aUserRelatedByToId);
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

            if ($this->filesScheduledForDeletion !== null) {
                if (!$this->filesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->filesScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    FileReferencesQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->filesScheduledForDeletion = null;
                }

                foreach ($this->getFiles() as $file) {
                    if ($file->isModified()) {
                        $file->save($con);
                    }
                }
            } elseif ($this->collFiles) {
                foreach ($this->collFiles as $file) {
                    if ($file->isModified()) {
                        $file->save($con);
                    }
                }
            }

            if ($this->assignmentReferenceIdsScheduledForDeletion !== null) {
                if (!$this->assignmentReferenceIdsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->assignmentReferenceIdsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    FileReferencesQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->assignmentReferenceIdsScheduledForDeletion = null;
                }

                foreach ($this->getassignmentReferenceIds() as $assignmentReferenceId) {
                    if ($assignmentReferenceId->isModified()) {
                        $assignmentReferenceId->save($con);
                    }
                }
            } elseif ($this->collassignmentReferenceIds) {
                foreach ($this->collassignmentReferenceIds as $assignmentReferenceId) {
                    if ($assignmentReferenceId->isModified()) {
                        $assignmentReferenceId->save($con);
                    }
                }
            }

            if ($this->studentAssignmentReferenceIdsScheduledForDeletion !== null) {
                if (!$this->studentAssignmentReferenceIdsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->studentAssignmentReferenceIdsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    FileReferencesQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->studentAssignmentReferenceIdsScheduledForDeletion = null;
                }

                foreach ($this->getstudentAssignmentReferenceIds() as $studentAssignmentReferenceId) {
                    if ($studentAssignmentReferenceId->isModified()) {
                        $studentAssignmentReferenceId->save($con);
                    }
                }
            } elseif ($this->collstudentAssignmentReferenceIds) {
                foreach ($this->collstudentAssignmentReferenceIds as $studentAssignmentReferenceId) {
                    if ($studentAssignmentReferenceId->isModified()) {
                        $studentAssignmentReferenceId->save($con);
                    }
                }
            }

            if ($this->fileReferencessScheduledForDeletion !== null) {
                if (!$this->fileReferencessScheduledForDeletion->isEmpty()) {
                    FileReferencesQuery::create()
                        ->filterByPrimaryKeys($this->fileReferencessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->fileReferencessScheduledForDeletion = null;
                }
            }

            if ($this->collFileReferencess !== null) {
                foreach ($this->collFileReferencess as $referrerFK) {
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

        $this->modifiedColumns[] = MessagePeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . MessagePeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(MessagePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(MessagePeer::THREAD_ID)) {
            $modifiedColumns[':p' . $index++]  = '`thread_id`';
        }
        if ($this->isColumnModified(MessagePeer::FROM_ID)) {
            $modifiedColumns[':p' . $index++]  = '`from_id`';
        }
        if ($this->isColumnModified(MessagePeer::TO_ID)) {
            $modifiedColumns[':p' . $index++]  = '`to_id`';
        }
        if ($this->isColumnModified(MessagePeer::IS_READ)) {
            $modifiedColumns[':p' . $index++]  = '`is_read`';
        }
        if ($this->isColumnModified(MessagePeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(MessagePeer::SUBJECT)) {
            $modifiedColumns[':p' . $index++]  = '`subject`';
        }
        if ($this->isColumnModified(MessagePeer::BODY)) {
            $modifiedColumns[':p' . $index++]  = '`body`';
        }
        if ($this->isColumnModified(MessagePeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }

        $sql = sprintf(
            'INSERT INTO `messages` (%s) VALUES (%s)',
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
                    case '`thread_id`':
                        $stmt->bindValue($identifier, $this->thread_id, PDO::PARAM_STR);
                        break;
                    case '`from_id`':
                        $stmt->bindValue($identifier, $this->from_id, PDO::PARAM_INT);
                        break;
                    case '`to_id`':
                        $stmt->bindValue($identifier, $this->to_id, PDO::PARAM_INT);
                        break;
                    case '`is_read`':
                        $stmt->bindValue($identifier, (int) $this->is_read, PDO::PARAM_INT);
                        break;
                    case '`user_id`':
                        $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
                        break;
                    case '`subject`':
                        $stmt->bindValue($identifier, $this->subject, PDO::PARAM_STR);
                        break;
                    case '`body`':
                        $stmt->bindValue($identifier, $this->body, PDO::PARAM_STR);
                        break;
                    case '`created_at`':
                        $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
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

            if ($this->aUserRelatedByUserId !== null) {
                if (!$this->aUserRelatedByUserId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aUserRelatedByUserId->getValidationFailures());
                }
            }

            if ($this->aUserRelatedByFromId !== null) {
                if (!$this->aUserRelatedByFromId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aUserRelatedByFromId->getValidationFailures());
                }
            }

            if ($this->aUserRelatedByToId !== null) {
                if (!$this->aUserRelatedByToId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aUserRelatedByToId->getValidationFailures());
                }
            }


            if (($retval = MessagePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collFileReferencess !== null) {
                    foreach ($this->collFileReferencess as $referrerFK) {
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
        $pos = MessagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getThreadId();
                break;
            case 2:
                return $this->getFromId();
                break;
            case 3:
                return $this->getToId();
                break;
            case 4:
                return $this->getIsRead();
                break;
            case 5:
                return $this->getUserId();
                break;
            case 6:
                return $this->getSubject();
                break;
            case 7:
                return $this->getBody();
                break;
            case 8:
                return $this->getCreatedAt();
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
        if (isset($alreadyDumpedObjects['Message'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Message'][$this->getPrimaryKey()] = true;
        $keys = MessagePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getThreadId(),
            $keys[2] => $this->getFromId(),
            $keys[3] => $this->getToId(),
            $keys[4] => $this->getIsRead(),
            $keys[5] => $this->getUserId(),
            $keys[6] => $this->getSubject(),
            $keys[7] => $this->getBody(),
            $keys[8] => $this->getCreatedAt(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aUserRelatedByUserId) {
                $result['UserRelatedByUserId'] = $this->aUserRelatedByUserId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aUserRelatedByFromId) {
                $result['UserRelatedByFromId'] = $this->aUserRelatedByFromId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aUserRelatedByToId) {
                $result['UserRelatedByToId'] = $this->aUserRelatedByToId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collFileReferencess) {
                $result['FileReferencess'] = $this->collFileReferencess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = MessagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setThreadId($value);
                break;
            case 2:
                $this->setFromId($value);
                break;
            case 3:
                $this->setToId($value);
                break;
            case 4:
                $this->setIsRead($value);
                break;
            case 5:
                $this->setUserId($value);
                break;
            case 6:
                $this->setSubject($value);
                break;
            case 7:
                $this->setBody($value);
                break;
            case 8:
                $this->setCreatedAt($value);
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
        $keys = MessagePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setThreadId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setFromId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setToId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setIsRead($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setUserId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setSubject($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setBody($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(MessagePeer::DATABASE_NAME);

        if ($this->isColumnModified(MessagePeer::ID)) $criteria->add(MessagePeer::ID, $this->id);
        if ($this->isColumnModified(MessagePeer::THREAD_ID)) $criteria->add(MessagePeer::THREAD_ID, $this->thread_id);
        if ($this->isColumnModified(MessagePeer::FROM_ID)) $criteria->add(MessagePeer::FROM_ID, $this->from_id);
        if ($this->isColumnModified(MessagePeer::TO_ID)) $criteria->add(MessagePeer::TO_ID, $this->to_id);
        if ($this->isColumnModified(MessagePeer::IS_READ)) $criteria->add(MessagePeer::IS_READ, $this->is_read);
        if ($this->isColumnModified(MessagePeer::USER_ID)) $criteria->add(MessagePeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(MessagePeer::SUBJECT)) $criteria->add(MessagePeer::SUBJECT, $this->subject);
        if ($this->isColumnModified(MessagePeer::BODY)) $criteria->add(MessagePeer::BODY, $this->body);
        if ($this->isColumnModified(MessagePeer::CREATED_AT)) $criteria->add(MessagePeer::CREATED_AT, $this->created_at);

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
        $criteria = new Criteria(MessagePeer::DATABASE_NAME);
        $criteria->add(MessagePeer::ID, $this->id);

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
     * @param object $copyObj An object of Message (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setThreadId($this->getThreadId());
        $copyObj->setFromId($this->getFromId());
        $copyObj->setToId($this->getToId());
        $copyObj->setIsRead($this->getIsRead());
        $copyObj->setUserId($this->getUserId());
        $copyObj->setSubject($this->getSubject());
        $copyObj->setBody($this->getBody());
        $copyObj->setCreatedAt($this->getCreatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getFileReferencess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFileReferences($relObj->copy($deepCopy));
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
     * @return Message Clone of current object.
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
     * @return MessagePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new MessagePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param             User $v
     * @return Message The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUserRelatedByUserId(User $v = null)
    {
        if ($v === null) {
            $this->setUserId(NULL);
        } else {
            $this->setUserId($v->getId());
        }

        $this->aUserRelatedByUserId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the User object, it will not be re-added.
        if ($v !== null) {
            $v->addMessageRelatedByUserId($this);
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
    public function getUserRelatedByUserId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aUserRelatedByUserId === null && ($this->user_id !== null) && $doQuery) {
            $this->aUserRelatedByUserId = UserQuery::create()->findPk($this->user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUserRelatedByUserId->addMessagesRelatedByUserId($this);
             */
        }

        return $this->aUserRelatedByUserId;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param             User $v
     * @return Message The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUserRelatedByFromId(User $v = null)
    {
        if ($v === null) {
            $this->setFromId(NULL);
        } else {
            $this->setFromId($v->getId());
        }

        $this->aUserRelatedByFromId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the User object, it will not be re-added.
        if ($v !== null) {
            $v->addMessageRelatedByFromId($this);
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
    public function getUserRelatedByFromId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aUserRelatedByFromId === null && ($this->from_id !== null) && $doQuery) {
            $this->aUserRelatedByFromId = UserQuery::create()->findPk($this->from_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUserRelatedByFromId->addMessagesRelatedByFromId($this);
             */
        }

        return $this->aUserRelatedByFromId;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param             User $v
     * @return Message The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUserRelatedByToId(User $v = null)
    {
        if ($v === null) {
            $this->setToId(NULL);
        } else {
            $this->setToId($v->getId());
        }

        $this->aUserRelatedByToId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the User object, it will not be re-added.
        if ($v !== null) {
            $v->addMessageRelatedByToId($this);
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
    public function getUserRelatedByToId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aUserRelatedByToId === null && ($this->to_id !== null) && $doQuery) {
            $this->aUserRelatedByToId = UserQuery::create()->findPk($this->to_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUserRelatedByToId->addMessagesRelatedByToId($this);
             */
        }

        return $this->aUserRelatedByToId;
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
        if ('FileReferences' == $relationName) {
            $this->initFileReferencess();
        }
    }

    /**
     * Clears out the collFileReferencess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Message The current object (for fluent API support)
     * @see        addFileReferencess()
     */
    public function clearFileReferencess()
    {
        $this->collFileReferencess = null; // important to set this to null since that means it is uninitialized
        $this->collFileReferencessPartial = null;

        return $this;
    }

    /**
     * reset is the collFileReferencess collection loaded partially
     *
     * @return void
     */
    public function resetPartialFileReferencess($v = true)
    {
        $this->collFileReferencessPartial = $v;
    }

    /**
     * Initializes the collFileReferencess collection.
     *
     * By default this just sets the collFileReferencess collection to an empty array (like clearcollFileReferencess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFileReferencess($overrideExisting = true)
    {
        if (null !== $this->collFileReferencess && !$overrideExisting) {
            return;
        }
        $this->collFileReferencess = new PropelObjectCollection();
        $this->collFileReferencess->setModel('FileReferences');
    }

    /**
     * Gets an array of FileReferences objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Message is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|FileReferences[] List of FileReferences objects
     * @throws PropelException
     */
    public function getFileReferencess($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collFileReferencessPartial && !$this->isNew();
        if (null === $this->collFileReferencess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFileReferencess) {
                // return empty collection
                $this->initFileReferencess();
            } else {
                $collFileReferencess = FileReferencesQuery::create(null, $criteria)
                    ->filterBymessageReferenceId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collFileReferencessPartial && count($collFileReferencess)) {
                      $this->initFileReferencess(false);

                      foreach($collFileReferencess as $obj) {
                        if (false == $this->collFileReferencess->contains($obj)) {
                          $this->collFileReferencess->append($obj);
                        }
                      }

                      $this->collFileReferencessPartial = true;
                    }

                    $collFileReferencess->getInternalIterator()->rewind();
                    return $collFileReferencess;
                }

                if($partial && $this->collFileReferencess) {
                    foreach($this->collFileReferencess as $obj) {
                        if($obj->isNew()) {
                            $collFileReferencess[] = $obj;
                        }
                    }
                }

                $this->collFileReferencess = $collFileReferencess;
                $this->collFileReferencessPartial = false;
            }
        }

        return $this->collFileReferencess;
    }

    /**
     * Sets a collection of FileReferences objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $fileReferencess A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Message The current object (for fluent API support)
     */
    public function setFileReferencess(PropelCollection $fileReferencess, PropelPDO $con = null)
    {
        $fileReferencessToDelete = $this->getFileReferencess(new Criteria(), $con)->diff($fileReferencess);

        $this->fileReferencessScheduledForDeletion = unserialize(serialize($fileReferencessToDelete));

        foreach ($fileReferencessToDelete as $fileReferencesRemoved) {
            $fileReferencesRemoved->setmessageReferenceId(null);
        }

        $this->collFileReferencess = null;
        foreach ($fileReferencess as $fileReferences) {
            $this->addFileReferences($fileReferences);
        }

        $this->collFileReferencess = $fileReferencess;
        $this->collFileReferencessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related FileReferences objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related FileReferences objects.
     * @throws PropelException
     */
    public function countFileReferencess(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collFileReferencessPartial && !$this->isNew();
        if (null === $this->collFileReferencess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFileReferencess) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getFileReferencess());
            }
            $query = FileReferencesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterBymessageReferenceId($this)
                ->count($con);
        }

        return count($this->collFileReferencess);
    }

    /**
     * Method called to associate a FileReferences object to this object
     * through the FileReferences foreign key attribute.
     *
     * @param    FileReferences $l FileReferences
     * @return Message The current object (for fluent API support)
     */
    public function addFileReferences(FileReferences $l)
    {
        if ($this->collFileReferencess === null) {
            $this->initFileReferencess();
            $this->collFileReferencessPartial = true;
        }
        if (!in_array($l, $this->collFileReferencess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddFileReferences($l);
        }

        return $this;
    }

    /**
     * @param	FileReferences $fileReferences The fileReferences object to add.
     */
    protected function doAddFileReferences($fileReferences)
    {
        $this->collFileReferencess[]= $fileReferences;
        $fileReferences->setmessageReferenceId($this);
    }

    /**
     * @param	FileReferences $fileReferences The fileReferences object to remove.
     * @return Message The current object (for fluent API support)
     */
    public function removeFileReferences($fileReferences)
    {
        if ($this->getFileReferencess()->contains($fileReferences)) {
            $this->collFileReferencess->remove($this->collFileReferencess->search($fileReferences));
            if (null === $this->fileReferencessScheduledForDeletion) {
                $this->fileReferencessScheduledForDeletion = clone $this->collFileReferencess;
                $this->fileReferencessScheduledForDeletion->clear();
            }
            $this->fileReferencessScheduledForDeletion[]= clone $fileReferences;
            $fileReferences->setmessageReferenceId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Message is new, it will return
     * an empty collection; or if this Message has previously
     * been saved, it will retrieve related FileReferencess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Message.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FileReferences[] List of FileReferences objects
     */
    public function getFileReferencessJoinFile($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FileReferencesQuery::create(null, $criteria);
        $query->joinWith('File', $join_behavior);

        return $this->getFileReferencess($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Message is new, it will return
     * an empty collection; or if this Message has previously
     * been saved, it will retrieve related FileReferencess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Message.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FileReferences[] List of FileReferences objects
     */
    public function getFileReferencessJoinassignmentReferenceId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FileReferencesQuery::create(null, $criteria);
        $query->joinWith('assignmentReferenceId', $join_behavior);

        return $this->getFileReferencess($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Message is new, it will return
     * an empty collection; or if this Message has previously
     * been saved, it will retrieve related FileReferencess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Message.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FileReferences[] List of FileReferences objects
     */
    public function getFileReferencessJoinstudentAssignmentReferenceId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FileReferencesQuery::create(null, $criteria);
        $query->joinWith('studentAssignmentReferenceId', $join_behavior);

        return $this->getFileReferencess($query, $con);
    }

    /**
     * Clears out the collFiles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Message The current object (for fluent API support)
     * @see        addFiles()
     */
    public function clearFiles()
    {
        $this->collFiles = null; // important to set this to null since that means it is uninitialized
        $this->collFilesPartial = null;

        return $this;
    }

    /**
     * Initializes the collFiles collection.
     *
     * By default this just sets the collFiles collection to an empty collection (like clearFiles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initFiles()
    {
        $this->collFiles = new PropelObjectCollection();
        $this->collFiles->setModel('File');
    }

    /**
     * Gets a collection of File objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Message is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|File[] List of File objects
     */
    public function getFiles($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collFiles || null !== $criteria) {
            if ($this->isNew() && null === $this->collFiles) {
                // return empty collection
                $this->initFiles();
            } else {
                $collFiles = FileQuery::create(null, $criteria)
                    ->filterBymessageReferenceId($this)
                    ->find($con);

                if (null !== $criteria) {
                    return $collFiles;
                }
                $this->collFiles = $collFiles;
            }
        }

        return $this->collFiles;
    }

    /**
     * Sets a collection of File objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $files A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Message The current object (for fluent API support)
     */
    public function setFiles(PropelCollection $files, PropelPDO $con = null)
    {
        $this->clearFiles();
        $currentFiles = $this->getFiles();

        $this->filesScheduledForDeletion = $currentFiles->diff($files);

        foreach ($files as $file) {
            if (!$currentFiles->contains($file)) {
                $this->doAddFile($file);
            }
        }

        $this->collFiles = $files;

        return $this;
    }

    /**
     * Gets the number of File objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related File objects
     */
    public function countFiles($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collFiles || null !== $criteria) {
            if ($this->isNew() && null === $this->collFiles) {
                return 0;
            } else {
                $query = FileQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterBymessageReferenceId($this)
                    ->count($con);
            }
        } else {
            return count($this->collFiles);
        }
    }

    /**
     * Associate a File object to this object
     * through the file_references cross reference table.
     *
     * @param  File $file The FileReferences object to relate
     * @return Message The current object (for fluent API support)
     */
    public function addFile(File $file)
    {
        if ($this->collFiles === null) {
            $this->initFiles();
        }
        if (!$this->collFiles->contains($file)) { // only add it if the **same** object is not already associated
            $this->doAddFile($file);

            $this->collFiles[]= $file;
        }

        return $this;
    }

    /**
     * @param	File $file The file object to add.
     */
    protected function doAddFile($file)
    {
        $fileReferences = new FileReferences();
        $fileReferences->setFile($file);
        $this->addFileReferences($fileReferences);
    }

    /**
     * Remove a File object to this object
     * through the file_references cross reference table.
     *
     * @param File $file The FileReferences object to relate
     * @return Message The current object (for fluent API support)
     */
    public function removeFile(File $file)
    {
        if ($this->getFiles()->contains($file)) {
            $this->collFiles->remove($this->collFiles->search($file));
            if (null === $this->filesScheduledForDeletion) {
                $this->filesScheduledForDeletion = clone $this->collFiles;
                $this->filesScheduledForDeletion->clear();
            }
            $this->filesScheduledForDeletion[]= $file;
        }

        return $this;
    }

    /**
     * Clears out the collassignmentReferenceIds collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Message The current object (for fluent API support)
     * @see        addassignmentReferenceIds()
     */
    public function clearassignmentReferenceIds()
    {
        $this->collassignmentReferenceIds = null; // important to set this to null since that means it is uninitialized
        $this->collassignmentReferenceIdsPartial = null;

        return $this;
    }

    /**
     * Initializes the collassignmentReferenceIds collection.
     *
     * By default this just sets the collassignmentReferenceIds collection to an empty collection (like clearassignmentReferenceIds());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initassignmentReferenceIds()
    {
        $this->collassignmentReferenceIds = new PropelObjectCollection();
        $this->collassignmentReferenceIds->setModel('Assignment');
    }

    /**
     * Gets a collection of Assignment objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Message is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Assignment[] List of Assignment objects
     */
    public function getassignmentReferenceIds($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collassignmentReferenceIds || null !== $criteria) {
            if ($this->isNew() && null === $this->collassignmentReferenceIds) {
                // return empty collection
                $this->initassignmentReferenceIds();
            } else {
                $collassignmentReferenceIds = AssignmentQuery::create(null, $criteria)
                    ->filterBymessageReferenceId($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collassignmentReferenceIds;
                }
                $this->collassignmentReferenceIds = $collassignmentReferenceIds;
            }
        }

        return $this->collassignmentReferenceIds;
    }

    /**
     * Sets a collection of Assignment objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $assignmentReferenceIds A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Message The current object (for fluent API support)
     */
    public function setassignmentReferenceIds(PropelCollection $assignmentReferenceIds, PropelPDO $con = null)
    {
        $this->clearassignmentReferenceIds();
        $currentassignmentReferenceIds = $this->getassignmentReferenceIds();

        $this->assignmentReferenceIdsScheduledForDeletion = $currentassignmentReferenceIds->diff($assignmentReferenceIds);

        foreach ($assignmentReferenceIds as $assignmentReferenceId) {
            if (!$currentassignmentReferenceIds->contains($assignmentReferenceId)) {
                $this->doAddassignmentReferenceId($assignmentReferenceId);
            }
        }

        $this->collassignmentReferenceIds = $assignmentReferenceIds;

        return $this;
    }

    /**
     * Gets the number of Assignment objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Assignment objects
     */
    public function countassignmentReferenceIds($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collassignmentReferenceIds || null !== $criteria) {
            if ($this->isNew() && null === $this->collassignmentReferenceIds) {
                return 0;
            } else {
                $query = AssignmentQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterBymessageReferenceId($this)
                    ->count($con);
            }
        } else {
            return count($this->collassignmentReferenceIds);
        }
    }

    /**
     * Associate a Assignment object to this object
     * through the file_references cross reference table.
     *
     * @param  Assignment $assignment The FileReferences object to relate
     * @return Message The current object (for fluent API support)
     */
    public function addassignmentReferenceId(Assignment $assignment)
    {
        if ($this->collassignmentReferenceIds === null) {
            $this->initassignmentReferenceIds();
        }
        if (!$this->collassignmentReferenceIds->contains($assignment)) { // only add it if the **same** object is not already associated
            $this->doAddassignmentReferenceId($assignment);

            $this->collassignmentReferenceIds[]= $assignment;
        }

        return $this;
    }

    /**
     * @param	assignmentReferenceId $assignmentReferenceId The assignmentReferenceId object to add.
     */
    protected function doAddassignmentReferenceId($assignmentReferenceId)
    {
        $fileReferences = new FileReferences();
        $fileReferences->setassignmentReferenceId($assignmentReferenceId);
        $this->addFileReferences($fileReferences);
    }

    /**
     * Remove a Assignment object to this object
     * through the file_references cross reference table.
     *
     * @param Assignment $assignment The FileReferences object to relate
     * @return Message The current object (for fluent API support)
     */
    public function removeassignmentReferenceId(Assignment $assignment)
    {
        if ($this->getassignmentReferenceIds()->contains($assignment)) {
            $this->collassignmentReferenceIds->remove($this->collassignmentReferenceIds->search($assignment));
            if (null === $this->assignmentReferenceIdsScheduledForDeletion) {
                $this->assignmentReferenceIdsScheduledForDeletion = clone $this->collassignmentReferenceIds;
                $this->assignmentReferenceIdsScheduledForDeletion->clear();
            }
            $this->assignmentReferenceIdsScheduledForDeletion[]= $assignment;
        }

        return $this;
    }

    /**
     * Clears out the collstudentAssignmentReferenceIds collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Message The current object (for fluent API support)
     * @see        addstudentAssignmentReferenceIds()
     */
    public function clearstudentAssignmentReferenceIds()
    {
        $this->collstudentAssignmentReferenceIds = null; // important to set this to null since that means it is uninitialized
        $this->collstudentAssignmentReferenceIdsPartial = null;

        return $this;
    }

    /**
     * Initializes the collstudentAssignmentReferenceIds collection.
     *
     * By default this just sets the collstudentAssignmentReferenceIds collection to an empty collection (like clearstudentAssignmentReferenceIds());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initstudentAssignmentReferenceIds()
    {
        $this->collstudentAssignmentReferenceIds = new PropelObjectCollection();
        $this->collstudentAssignmentReferenceIds->setModel('StudentAssignment');
    }

    /**
     * Gets a collection of StudentAssignment objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Message is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|StudentAssignment[] List of StudentAssignment objects
     */
    public function getstudentAssignmentReferenceIds($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collstudentAssignmentReferenceIds || null !== $criteria) {
            if ($this->isNew() && null === $this->collstudentAssignmentReferenceIds) {
                // return empty collection
                $this->initstudentAssignmentReferenceIds();
            } else {
                $collstudentAssignmentReferenceIds = StudentAssignmentQuery::create(null, $criteria)
                    ->filterBymessageReferenceId($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collstudentAssignmentReferenceIds;
                }
                $this->collstudentAssignmentReferenceIds = $collstudentAssignmentReferenceIds;
            }
        }

        return $this->collstudentAssignmentReferenceIds;
    }

    /**
     * Sets a collection of StudentAssignment objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $studentAssignmentReferenceIds A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Message The current object (for fluent API support)
     */
    public function setstudentAssignmentReferenceIds(PropelCollection $studentAssignmentReferenceIds, PropelPDO $con = null)
    {
        $this->clearstudentAssignmentReferenceIds();
        $currentstudentAssignmentReferenceIds = $this->getstudentAssignmentReferenceIds();

        $this->studentAssignmentReferenceIdsScheduledForDeletion = $currentstudentAssignmentReferenceIds->diff($studentAssignmentReferenceIds);

        foreach ($studentAssignmentReferenceIds as $studentAssignmentReferenceId) {
            if (!$currentstudentAssignmentReferenceIds->contains($studentAssignmentReferenceId)) {
                $this->doAddstudentAssignmentReferenceId($studentAssignmentReferenceId);
            }
        }

        $this->collstudentAssignmentReferenceIds = $studentAssignmentReferenceIds;

        return $this;
    }

    /**
     * Gets the number of StudentAssignment objects related by a many-to-many relationship
     * to the current object by way of the file_references cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related StudentAssignment objects
     */
    public function countstudentAssignmentReferenceIds($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collstudentAssignmentReferenceIds || null !== $criteria) {
            if ($this->isNew() && null === $this->collstudentAssignmentReferenceIds) {
                return 0;
            } else {
                $query = StudentAssignmentQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterBymessageReferenceId($this)
                    ->count($con);
            }
        } else {
            return count($this->collstudentAssignmentReferenceIds);
        }
    }

    /**
     * Associate a StudentAssignment object to this object
     * through the file_references cross reference table.
     *
     * @param  StudentAssignment $studentAssignment The FileReferences object to relate
     * @return Message The current object (for fluent API support)
     */
    public function addstudentAssignmentReferenceId(StudentAssignment $studentAssignment)
    {
        if ($this->collstudentAssignmentReferenceIds === null) {
            $this->initstudentAssignmentReferenceIds();
        }
        if (!$this->collstudentAssignmentReferenceIds->contains($studentAssignment)) { // only add it if the **same** object is not already associated
            $this->doAddstudentAssignmentReferenceId($studentAssignment);

            $this->collstudentAssignmentReferenceIds[]= $studentAssignment;
        }

        return $this;
    }

    /**
     * @param	studentAssignmentReferenceId $studentAssignmentReferenceId The studentAssignmentReferenceId object to add.
     */
    protected function doAddstudentAssignmentReferenceId($studentAssignmentReferenceId)
    {
        $fileReferences = new FileReferences();
        $fileReferences->setstudentAssignmentReferenceId($studentAssignmentReferenceId);
        $this->addFileReferences($fileReferences);
    }

    /**
     * Remove a StudentAssignment object to this object
     * through the file_references cross reference table.
     *
     * @param StudentAssignment $studentAssignment The FileReferences object to relate
     * @return Message The current object (for fluent API support)
     */
    public function removestudentAssignmentReferenceId(StudentAssignment $studentAssignment)
    {
        if ($this->getstudentAssignmentReferenceIds()->contains($studentAssignment)) {
            $this->collstudentAssignmentReferenceIds->remove($this->collstudentAssignmentReferenceIds->search($studentAssignment));
            if (null === $this->studentAssignmentReferenceIdsScheduledForDeletion) {
                $this->studentAssignmentReferenceIdsScheduledForDeletion = clone $this->collstudentAssignmentReferenceIds;
                $this->studentAssignmentReferenceIdsScheduledForDeletion->clear();
            }
            $this->studentAssignmentReferenceIdsScheduledForDeletion[]= $studentAssignment;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->thread_id = null;
        $this->from_id = null;
        $this->to_id = null;
        $this->is_read = null;
        $this->user_id = null;
        $this->subject = null;
        $this->body = null;
        $this->created_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collFileReferencess) {
                foreach ($this->collFileReferencess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFiles) {
                foreach ($this->collFiles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collassignmentReferenceIds) {
                foreach ($this->collassignmentReferenceIds as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collstudentAssignmentReferenceIds) {
                foreach ($this->collstudentAssignmentReferenceIds as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aUserRelatedByUserId instanceof Persistent) {
              $this->aUserRelatedByUserId->clearAllReferences($deep);
            }
            if ($this->aUserRelatedByFromId instanceof Persistent) {
              $this->aUserRelatedByFromId->clearAllReferences($deep);
            }
            if ($this->aUserRelatedByToId instanceof Persistent) {
              $this->aUserRelatedByToId->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collFileReferencess instanceof PropelCollection) {
            $this->collFileReferencess->clearIterator();
        }
        $this->collFileReferencess = null;
        if ($this->collFiles instanceof PropelCollection) {
            $this->collFiles->clearIterator();
        }
        $this->collFiles = null;
        if ($this->collassignmentReferenceIds instanceof PropelCollection) {
            $this->collassignmentReferenceIds->clearIterator();
        }
        $this->collassignmentReferenceIds = null;
        if ($this->collstudentAssignmentReferenceIds instanceof PropelCollection) {
            $this->collstudentAssignmentReferenceIds->clearIterator();
        }
        $this->collstudentAssignmentReferenceIds = null;
        $this->aUserRelatedByUserId = null;
        $this->aUserRelatedByFromId = null;
        $this->aUserRelatedByToId = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(MessagePeer::DEFAULT_STRING_FORMAT);
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
