<?php

namespace Zerebral\BusinessBundle\Model\Assignment\om;

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
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;
use Zerebral\BusinessBundle\Model\File\File;
use Zerebral\BusinessBundle\Model\File\FileQuery;
use Zerebral\BusinessBundle\Model\File\FileReferences;
use Zerebral\BusinessBundle\Model\File\FileReferencesQuery;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\StudentQuery;

abstract class BaseStudentAssignment extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignmentPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        StudentAssignmentPeer
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
     * The value for the student_id field.
     * @var        int
     */
    protected $student_id;

    /**
     * The value for the assignment_id field.
     * @var        int
     */
    protected $assignment_id;

    /**
     * The value for the is_submitted field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_submitted;

    /**
     * The value for the grading field.
     * @var        string
     */
    protected $grading;

    /**
     * The value for the grading_comment field.
     * @var        string
     */
    protected $grading_comment;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * @var        Student
     */
    protected $aStudent;

    /**
     * @var        Assignment
     */
    protected $aAssignment;

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
    protected $fileReferencessScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->is_submitted = false;
    }

    /**
     * Initializes internal state of BaseStudentAssignment object.
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
     * Get the [student_id] column value.
     *
     * @return int
     */
    public function getStudentId()
    {
        return $this->student_id;
    }

    /**
     * Get the [assignment_id] column value.
     *
     * @return int
     */
    public function getAssignmentId()
    {
        return $this->assignment_id;
    }

    /**
     * Get the [is_submitted] column value.
     *
     * @return boolean
     */
    public function getIsSubmitted()
    {
        return $this->is_submitted;
    }

    /**
     * Get the [grading] column value.
     *
     * @return string
     */
    public function getGrading()
    {
        return $this->grading;
    }

    /**
     * Get the [grading_comment] column value.
     *
     * @return string
     */
    public function getGradingComment()
    {
        return $this->grading_comment;
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
     * @return StudentAssignment The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = StudentAssignmentPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [student_id] column.
     *
     * @param int $v new value
     * @return StudentAssignment The current object (for fluent API support)
     */
    public function setStudentId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->student_id !== $v) {
            $this->student_id = $v;
            $this->modifiedColumns[] = StudentAssignmentPeer::STUDENT_ID;
        }

        if ($this->aStudent !== null && $this->aStudent->getId() !== $v) {
            $this->aStudent = null;
        }


        return $this;
    } // setStudentId()

    /**
     * Set the value of [assignment_id] column.
     *
     * @param int $v new value
     * @return StudentAssignment The current object (for fluent API support)
     */
    public function setAssignmentId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->assignment_id !== $v) {
            $this->assignment_id = $v;
            $this->modifiedColumns[] = StudentAssignmentPeer::ASSIGNMENT_ID;
        }

        if ($this->aAssignment !== null && $this->aAssignment->getId() !== $v) {
            $this->aAssignment = null;
        }


        return $this;
    } // setAssignmentId()

    /**
     * Sets the value of the [is_submitted] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return StudentAssignment The current object (for fluent API support)
     */
    public function setIsSubmitted($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_submitted !== $v) {
            $this->is_submitted = $v;
            $this->modifiedColumns[] = StudentAssignmentPeer::IS_SUBMITTED;
        }


        return $this;
    } // setIsSubmitted()

    /**
     * Set the value of [grading] column.
     *
     * @param string $v new value
     * @return StudentAssignment The current object (for fluent API support)
     */
    public function setGrading($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->grading !== $v) {
            $this->grading = $v;
            $this->modifiedColumns[] = StudentAssignmentPeer::GRADING;
        }


        return $this;
    } // setGrading()

    /**
     * Set the value of [grading_comment] column.
     *
     * @param string $v new value
     * @return StudentAssignment The current object (for fluent API support)
     */
    public function setGradingComment($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->grading_comment !== $v) {
            $this->grading_comment = $v;
            $this->modifiedColumns[] = StudentAssignmentPeer::GRADING_COMMENT;
        }


        return $this;
    } // setGradingComment()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return StudentAssignment The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = StudentAssignmentPeer::CREATED_AT;
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
            if ($this->is_submitted !== false) {
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
            $this->student_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->assignment_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->is_submitted = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
            $this->grading = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->grading_comment = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->created_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 7; // 7 = StudentAssignmentPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating StudentAssignment object", $e);
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

        if ($this->aStudent !== null && $this->student_id !== $this->aStudent->getId()) {
            $this->aStudent = null;
        }
        if ($this->aAssignment !== null && $this->assignment_id !== $this->aAssignment->getId()) {
            $this->aAssignment = null;
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
            $con = Propel::getConnection(StudentAssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = StudentAssignmentPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aStudent = null;
            $this->aAssignment = null;
            $this->collFileReferencess = null;

            $this->collFiles = null;
            $this->collassignmentReferenceIds = null;
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
            $con = Propel::getConnection(StudentAssignmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = StudentAssignmentQuery::create()
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
            $con = Propel::getConnection(StudentAssignmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        // $isInsert = $this->isNew();
        try {
            $ret = true;
            if ($this->isNew() || $this->isModified()) {
                $ret = $this->preSave($con);
            // event behavior
            EventDispatcherProxy::trigger('model.save.pre', new ModelEvent($this));
            }
            if ($this->isNew()) {
                $ret = $ret && $this->preInsert($con);
                // event behavior
                EventDispatcherProxy::trigger('model.insert.pre', new ModelEvent($this));
            } elseif ($this->isModified()) {
                $ret = $ret && $this->preUpdate($con);
                // event behavior
                EventDispatcherProxy::trigger(array('update.pre', 'model.update.pre'), new ModelEvent($this));
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                StudentAssignmentPeer::addInstanceToPool($this);
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
            $isInsert = $this->isNew();
            $isUpdate = $this->isModified();

            // We call the save method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aStudent !== null) {
                if ($this->aStudent->isModified() || $this->aStudent->isNew()) {
                    $affectedRows += $this->aStudent->save($con);
                }
                $this->setStudent($this->aStudent);
            }

            if ($this->aAssignment !== null) {
                if ($this->aAssignment->isModified() || $this->aAssignment->isNew()) {
                    $affectedRows += $this->aAssignment->save($con);
                }
                $this->setAssignment($this->aAssignment);
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

            if ($isInsert) {
                $this->postInsert($con);
                    // event behavior
                    EventDispatcherProxy::trigger('model.insert.post', new ModelEvent($this));
            }
            if ($isUpdate) {
                $this->postUpdate($con);
                    // event behavior
                    EventDispatcherProxy::trigger(array('update.post', 'model.update.post'), new ModelEvent($this));
            }
            if ($isUpdate || $isInsert) {
                $this->postSave($con);
                    // event behavior
                    EventDispatcherProxy::trigger('model.save.post', new ModelEvent($this));
            }
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

        $this->modifiedColumns[] = StudentAssignmentPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . StudentAssignmentPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(StudentAssignmentPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(StudentAssignmentPeer::STUDENT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`student_id`';
        }
        if ($this->isColumnModified(StudentAssignmentPeer::ASSIGNMENT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`assignment_id`';
        }
        if ($this->isColumnModified(StudentAssignmentPeer::IS_SUBMITTED)) {
            $modifiedColumns[':p' . $index++]  = '`is_submitted`';
        }
        if ($this->isColumnModified(StudentAssignmentPeer::GRADING)) {
            $modifiedColumns[':p' . $index++]  = '`grading`';
        }
        if ($this->isColumnModified(StudentAssignmentPeer::GRADING_COMMENT)) {
            $modifiedColumns[':p' . $index++]  = '`grading_comment`';
        }
        if ($this->isColumnModified(StudentAssignmentPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }

        $sql = sprintf(
            'INSERT INTO `student_assignments` (%s) VALUES (%s)',
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
                    case '`student_id`':
                        $stmt->bindValue($identifier, $this->student_id, PDO::PARAM_INT);
                        break;
                    case '`assignment_id`':
                        $stmt->bindValue($identifier, $this->assignment_id, PDO::PARAM_INT);
                        break;
                    case '`is_submitted`':
                        $stmt->bindValue($identifier, (int) $this->is_submitted, PDO::PARAM_INT);
                        break;
                    case '`grading`':
                        $stmt->bindValue($identifier, $this->grading, PDO::PARAM_STR);
                        break;
                    case '`grading_comment`':
                        $stmt->bindValue($identifier, $this->grading_comment, PDO::PARAM_STR);
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

            if ($this->aStudent !== null) {
                if (!$this->aStudent->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aStudent->getValidationFailures());
                }
            }

            if ($this->aAssignment !== null) {
                if (!$this->aAssignment->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAssignment->getValidationFailures());
                }
            }


            if (($retval = StudentAssignmentPeer::doValidate($this, $columns)) !== true) {
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
        $pos = StudentAssignmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getStudentId();
                break;
            case 2:
                return $this->getAssignmentId();
                break;
            case 3:
                return $this->getIsSubmitted();
                break;
            case 4:
                return $this->getGrading();
                break;
            case 5:
                return $this->getGradingComment();
                break;
            case 6:
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
        if (isset($alreadyDumpedObjects['StudentAssignment'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['StudentAssignment'][$this->getPrimaryKey()] = true;
        $keys = StudentAssignmentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getStudentId(),
            $keys[2] => $this->getAssignmentId(),
            $keys[3] => $this->getIsSubmitted(),
            $keys[4] => $this->getGrading(),
            $keys[5] => $this->getGradingComment(),
            $keys[6] => $this->getCreatedAt(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aStudent) {
                $result['Student'] = $this->aStudent->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aAssignment) {
                $result['Assignment'] = $this->aAssignment->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = StudentAssignmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setStudentId($value);
                break;
            case 2:
                $this->setAssignmentId($value);
                break;
            case 3:
                $this->setIsSubmitted($value);
                break;
            case 4:
                $this->setGrading($value);
                break;
            case 5:
                $this->setGradingComment($value);
                break;
            case 6:
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
        $keys = StudentAssignmentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setStudentId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setAssignmentId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setIsSubmitted($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setGrading($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setGradingComment($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCreatedAt($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(StudentAssignmentPeer::DATABASE_NAME);

        if ($this->isColumnModified(StudentAssignmentPeer::ID)) $criteria->add(StudentAssignmentPeer::ID, $this->id);
        if ($this->isColumnModified(StudentAssignmentPeer::STUDENT_ID)) $criteria->add(StudentAssignmentPeer::STUDENT_ID, $this->student_id);
        if ($this->isColumnModified(StudentAssignmentPeer::ASSIGNMENT_ID)) $criteria->add(StudentAssignmentPeer::ASSIGNMENT_ID, $this->assignment_id);
        if ($this->isColumnModified(StudentAssignmentPeer::IS_SUBMITTED)) $criteria->add(StudentAssignmentPeer::IS_SUBMITTED, $this->is_submitted);
        if ($this->isColumnModified(StudentAssignmentPeer::GRADING)) $criteria->add(StudentAssignmentPeer::GRADING, $this->grading);
        if ($this->isColumnModified(StudentAssignmentPeer::GRADING_COMMENT)) $criteria->add(StudentAssignmentPeer::GRADING_COMMENT, $this->grading_comment);
        if ($this->isColumnModified(StudentAssignmentPeer::CREATED_AT)) $criteria->add(StudentAssignmentPeer::CREATED_AT, $this->created_at);

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
        $criteria = new Criteria(StudentAssignmentPeer::DATABASE_NAME);
        $criteria->add(StudentAssignmentPeer::ID, $this->id);

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
     * @param object $copyObj An object of StudentAssignment (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setStudentId($this->getStudentId());
        $copyObj->setAssignmentId($this->getAssignmentId());
        $copyObj->setIsSubmitted($this->getIsSubmitted());
        $copyObj->setGrading($this->getGrading());
        $copyObj->setGradingComment($this->getGradingComment());
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
     * @return StudentAssignment Clone of current object.
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
     * @return StudentAssignmentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new StudentAssignmentPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Student object.
     *
     * @param             Student $v
     * @return StudentAssignment The current object (for fluent API support)
     * @throws PropelException
     */
    public function setStudent(Student $v = null)
    {
        if ($v === null) {
            $this->setStudentId(NULL);
        } else {
            $this->setStudentId($v->getId());
        }

        $this->aStudent = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Student object, it will not be re-added.
        if ($v !== null) {
            $v->addStudentAssignment($this);
        }


        return $this;
    }


    /**
     * Get the associated Student object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Student The associated Student object.
     * @throws PropelException
     */
    public function getStudent(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aStudent === null && ($this->student_id !== null) && $doQuery) {
            $this->aStudent = StudentQuery::create()->findPk($this->student_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aStudent->addStudentAssignments($this);
             */
        }

        return $this->aStudent;
    }

    /**
     * Declares an association between this object and a Assignment object.
     *
     * @param             Assignment $v
     * @return StudentAssignment The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAssignment(Assignment $v = null)
    {
        if ($v === null) {
            $this->setAssignmentId(NULL);
        } else {
            $this->setAssignmentId($v->getId());
        }

        $this->aAssignment = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Assignment object, it will not be re-added.
        if ($v !== null) {
            $v->addStudentAssignment($this);
        }


        return $this;
    }


    /**
     * Get the associated Assignment object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Assignment The associated Assignment object.
     * @throws PropelException
     */
    public function getAssignment(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAssignment === null && ($this->assignment_id !== null) && $doQuery) {
            $this->aAssignment = AssignmentQuery::create()->findPk($this->assignment_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAssignment->addStudentAssignments($this);
             */
        }

        return $this->aAssignment;
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
     * @return StudentAssignment The current object (for fluent API support)
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
     * If this StudentAssignment is new, it will return
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
                    ->filterBystudentAssignmentReferenceId($this)
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
     * @return StudentAssignment The current object (for fluent API support)
     */
    public function setFileReferencess(PropelCollection $fileReferencess, PropelPDO $con = null)
    {
        $fileReferencessToDelete = $this->getFileReferencess(new Criteria(), $con)->diff($fileReferencess);

        $this->fileReferencessScheduledForDeletion = unserialize(serialize($fileReferencessToDelete));

        foreach ($fileReferencessToDelete as $fileReferencesRemoved) {
            $fileReferencesRemoved->setstudentAssignmentReferenceId(null);
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
                ->filterBystudentAssignmentReferenceId($this)
                ->count($con);
        }

        return count($this->collFileReferencess);
    }

    /**
     * Method called to associate a FileReferences object to this object
     * through the FileReferences foreign key attribute.
     *
     * @param    FileReferences $l FileReferences
     * @return StudentAssignment The current object (for fluent API support)
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
        $fileReferences->setstudentAssignmentReferenceId($this);
    }

    /**
     * @param	FileReferences $fileReferences The fileReferences object to remove.
     * @return StudentAssignment The current object (for fluent API support)
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
            $fileReferences->setstudentAssignmentReferenceId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this StudentAssignment is new, it will return
     * an empty collection; or if this StudentAssignment has previously
     * been saved, it will retrieve related FileReferencess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in StudentAssignment.
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
     * Otherwise if this StudentAssignment is new, it will return
     * an empty collection; or if this StudentAssignment has previously
     * been saved, it will retrieve related FileReferencess from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in StudentAssignment.
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
     * Clears out the collFiles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return StudentAssignment The current object (for fluent API support)
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
     * If this StudentAssignment is new, it will return
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
                    ->filterBystudentAssignmentReferenceId($this)
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
     * @return StudentAssignment The current object (for fluent API support)
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
                    ->filterBystudentAssignmentReferenceId($this)
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
     * @return StudentAssignment The current object (for fluent API support)
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
     * @return StudentAssignment The current object (for fluent API support)
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
     * @return StudentAssignment The current object (for fluent API support)
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
     * If this StudentAssignment is new, it will return
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
                    ->filterBystudentAssignmentReferenceId($this)
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
     * @return StudentAssignment The current object (for fluent API support)
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
                    ->filterBystudentAssignmentReferenceId($this)
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
     * @return StudentAssignment The current object (for fluent API support)
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
     * @return StudentAssignment The current object (for fluent API support)
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
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->student_id = null;
        $this->assignment_id = null;
        $this->is_submitted = null;
        $this->grading = null;
        $this->grading_comment = null;
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
            if ($this->aStudent instanceof Persistent) {
              $this->aStudent->clearAllReferences($deep);
            }
            if ($this->aAssignment instanceof Persistent) {
              $this->aAssignment->clearAllReferences($deep);
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
        $this->aStudent = null;
        $this->aAssignment = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(StudentAssignmentPeer::DEFAULT_STRING_FORMAT);
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
