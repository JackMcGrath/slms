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
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentPeer;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignment;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CourseQuery;
use Zerebral\BusinessBundle\Model\User\Teacher;
use Zerebral\BusinessBundle\Model\User\TeacherQuery;

abstract class BaseAssignment extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AssignmentPeer
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
     * The value for the teacher_id field.
     * @var        int
     */
    protected $teacher_id;

    /**
     * The value for the course_id field.
     * @var        int
     */
    protected $course_id;

    /**
     * The value for the assignment_category_id field.
     * @var        int
     */
    protected $assignment_category_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the max_points field.
     * @var        int
     */
    protected $max_points;

    /**
     * The value for the due_at field.
     * @var        string
     */
    protected $due_at;

    /**
     * @var        Teacher
     */
    protected $aTeacher;

    /**
     * @var        Course
     */
    protected $aCourse;

    /**
     * @var        AssignmentCategory
     */
    protected $aAssignmentCategory;

    /**
     * @var        PropelObjectCollection|StudentAssignment[] Collection to store aggregation of StudentAssignment objects.
     */
    protected $collStudentAssignments;
    protected $collStudentAssignmentsPartial;

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
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [teacher_id] column value.
     *
     * @return int
     */
    public function getTeacherId()
    {
        return $this->teacher_id;
    }

    /**
     * Get the [course_id] column value.
     *
     * @return int
     */
    public function getCourseId()
    {
        return $this->course_id;
    }

    /**
     * Get the [assignment_category_id] column value.
     *
     * @return int
     */
    public function getAssignmentCategoryId()
    {
        return $this->assignment_category_id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the [max_points] column value.
     *
     * @return int
     */
    public function getMaxPoints()
    {
        return $this->max_points;
    }

    /**
     * Get the [optionally formatted] temporal [due_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getDueAt($format = null)
    {
        if ($this->due_at === null) {
            return null;
        }

        if ($this->due_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->due_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->due_at, true), $x);
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
     * @return Assignment The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = AssignmentPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [teacher_id] column.
     *
     * @param int $v new value
     * @return Assignment The current object (for fluent API support)
     */
    public function setTeacherId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->teacher_id !== $v) {
            $this->teacher_id = $v;
            $this->modifiedColumns[] = AssignmentPeer::TEACHER_ID;
        }

        if ($this->aTeacher !== null && $this->aTeacher->getId() !== $v) {
            $this->aTeacher = null;
        }


        return $this;
    } // setTeacherId()

    /**
     * Set the value of [course_id] column.
     *
     * @param int $v new value
     * @return Assignment The current object (for fluent API support)
     */
    public function setCourseId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->course_id !== $v) {
            $this->course_id = $v;
            $this->modifiedColumns[] = AssignmentPeer::COURSE_ID;
        }

        if ($this->aCourse !== null && $this->aCourse->getId() !== $v) {
            $this->aCourse = null;
        }


        return $this;
    } // setCourseId()

    /**
     * Set the value of [assignment_category_id] column.
     *
     * @param int $v new value
     * @return Assignment The current object (for fluent API support)
     */
    public function setAssignmentCategoryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->assignment_category_id !== $v) {
            $this->assignment_category_id = $v;
            $this->modifiedColumns[] = AssignmentPeer::ASSIGNMENT_CATEGORY_ID;
        }

        if ($this->aAssignmentCategory !== null && $this->aAssignmentCategory->getId() !== $v) {
            $this->aAssignmentCategory = null;
        }


        return $this;
    } // setAssignmentCategoryId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return Assignment The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = AssignmentPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [description] column.
     *
     * @param string $v new value
     * @return Assignment The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = AssignmentPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Set the value of [max_points] column.
     *
     * @param int $v new value
     * @return Assignment The current object (for fluent API support)
     */
    public function setMaxPoints($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->max_points !== $v) {
            $this->max_points = $v;
            $this->modifiedColumns[] = AssignmentPeer::MAX_POINTS;
        }


        return $this;
    } // setMaxPoints()

    /**
     * Sets the value of [due_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Assignment The current object (for fluent API support)
     */
    public function setDueAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->due_at !== null || $dt !== null) {
            $currentDateAsString = ($this->due_at !== null && $tmpDt = new DateTime($this->due_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->due_at = $newDateAsString;
                $this->modifiedColumns[] = AssignmentPeer::DUE_AT;
            }
        } // if either are not null


        return $this;
    } // setDueAt()

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
            $this->teacher_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->course_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->assignment_category_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->description = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->max_points = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->due_at = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 8; // 8 = AssignmentPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Assignment object", $e);
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

        if ($this->aTeacher !== null && $this->teacher_id !== $this->aTeacher->getId()) {
            $this->aTeacher = null;
        }
        if ($this->aCourse !== null && $this->course_id !== $this->aCourse->getId()) {
            $this->aCourse = null;
        }
        if ($this->aAssignmentCategory !== null && $this->assignment_category_id !== $this->aAssignmentCategory->getId()) {
            $this->aAssignmentCategory = null;
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
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = AssignmentPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aTeacher = null;
            $this->aCourse = null;
            $this->aAssignmentCategory = null;
            $this->collStudentAssignments = null;

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
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = AssignmentQuery::create()
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
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                AssignmentPeer::addInstanceToPool($this);
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

            if ($this->aTeacher !== null) {
                if ($this->aTeacher->isModified() || $this->aTeacher->isNew()) {
                    $affectedRows += $this->aTeacher->save($con);
                }
                $this->setTeacher($this->aTeacher);
            }

            if ($this->aCourse !== null) {
                if ($this->aCourse->isModified() || $this->aCourse->isNew()) {
                    $affectedRows += $this->aCourse->save($con);
                }
                $this->setCourse($this->aCourse);
            }

            if ($this->aAssignmentCategory !== null) {
                if ($this->aAssignmentCategory->isModified() || $this->aAssignmentCategory->isNew()) {
                    $affectedRows += $this->aAssignmentCategory->save($con);
                }
                $this->setAssignmentCategory($this->aAssignmentCategory);
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

        $this->modifiedColumns[] = AssignmentPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AssignmentPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AssignmentPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(AssignmentPeer::TEACHER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`teacher_id`';
        }
        if ($this->isColumnModified(AssignmentPeer::COURSE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`course_id`';
        }
        if ($this->isColumnModified(AssignmentPeer::ASSIGNMENT_CATEGORY_ID)) {
            $modifiedColumns[':p' . $index++]  = '`assignment_category_id`';
        }
        if ($this->isColumnModified(AssignmentPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`name`';
        }
        if ($this->isColumnModified(AssignmentPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '`description`';
        }
        if ($this->isColumnModified(AssignmentPeer::MAX_POINTS)) {
            $modifiedColumns[':p' . $index++]  = '`max_points`';
        }
        if ($this->isColumnModified(AssignmentPeer::DUE_AT)) {
            $modifiedColumns[':p' . $index++]  = '`due_at`';
        }

        $sql = sprintf(
            'INSERT INTO `assignments` (%s) VALUES (%s)',
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
                    case '`teacher_id`':
                        $stmt->bindValue($identifier, $this->teacher_id, PDO::PARAM_INT);
                        break;
                    case '`course_id`':
                        $stmt->bindValue($identifier, $this->course_id, PDO::PARAM_INT);
                        break;
                    case '`assignment_category_id`':
                        $stmt->bindValue($identifier, $this->assignment_category_id, PDO::PARAM_INT);
                        break;
                    case '`name`':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '`description`':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '`max_points`':
                        $stmt->bindValue($identifier, $this->max_points, PDO::PARAM_INT);
                        break;
                    case '`due_at`':
                        $stmt->bindValue($identifier, $this->due_at, PDO::PARAM_STR);
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

            if ($this->aTeacher !== null) {
                if (!$this->aTeacher->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aTeacher->getValidationFailures());
                }
            }

            if ($this->aCourse !== null) {
                if (!$this->aCourse->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCourse->getValidationFailures());
                }
            }

            if ($this->aAssignmentCategory !== null) {
                if (!$this->aAssignmentCategory->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAssignmentCategory->getValidationFailures());
                }
            }


            if (($retval = AssignmentPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collStudentAssignments !== null) {
                    foreach ($this->collStudentAssignments as $referrerFK) {
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
        $pos = AssignmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTeacherId();
                break;
            case 2:
                return $this->getCourseId();
                break;
            case 3:
                return $this->getAssignmentCategoryId();
                break;
            case 4:
                return $this->getName();
                break;
            case 5:
                return $this->getDescription();
                break;
            case 6:
                return $this->getMaxPoints();
                break;
            case 7:
                return $this->getDueAt();
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
        if (isset($alreadyDumpedObjects['Assignment'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Assignment'][$this->getPrimaryKey()] = true;
        $keys = AssignmentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTeacherId(),
            $keys[2] => $this->getCourseId(),
            $keys[3] => $this->getAssignmentCategoryId(),
            $keys[4] => $this->getName(),
            $keys[5] => $this->getDescription(),
            $keys[6] => $this->getMaxPoints(),
            $keys[7] => $this->getDueAt(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aTeacher) {
                $result['Teacher'] = $this->aTeacher->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCourse) {
                $result['Course'] = $this->aCourse->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aAssignmentCategory) {
                $result['AssignmentCategory'] = $this->aAssignmentCategory->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collStudentAssignments) {
                $result['StudentAssignments'] = $this->collStudentAssignments->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = AssignmentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setTeacherId($value);
                break;
            case 2:
                $this->setCourseId($value);
                break;
            case 3:
                $this->setAssignmentCategoryId($value);
                break;
            case 4:
                $this->setName($value);
                break;
            case 5:
                $this->setDescription($value);
                break;
            case 6:
                $this->setMaxPoints($value);
                break;
            case 7:
                $this->setDueAt($value);
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
        $keys = AssignmentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setTeacherId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCourseId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setAssignmentCategoryId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setName($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDescription($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setMaxPoints($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDueAt($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AssignmentPeer::DATABASE_NAME);

        if ($this->isColumnModified(AssignmentPeer::ID)) $criteria->add(AssignmentPeer::ID, $this->id);
        if ($this->isColumnModified(AssignmentPeer::TEACHER_ID)) $criteria->add(AssignmentPeer::TEACHER_ID, $this->teacher_id);
        if ($this->isColumnModified(AssignmentPeer::COURSE_ID)) $criteria->add(AssignmentPeer::COURSE_ID, $this->course_id);
        if ($this->isColumnModified(AssignmentPeer::ASSIGNMENT_CATEGORY_ID)) $criteria->add(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, $this->assignment_category_id);
        if ($this->isColumnModified(AssignmentPeer::NAME)) $criteria->add(AssignmentPeer::NAME, $this->name);
        if ($this->isColumnModified(AssignmentPeer::DESCRIPTION)) $criteria->add(AssignmentPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(AssignmentPeer::MAX_POINTS)) $criteria->add(AssignmentPeer::MAX_POINTS, $this->max_points);
        if ($this->isColumnModified(AssignmentPeer::DUE_AT)) $criteria->add(AssignmentPeer::DUE_AT, $this->due_at);

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
        $criteria = new Criteria(AssignmentPeer::DATABASE_NAME);
        $criteria->add(AssignmentPeer::ID, $this->id);

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
     * @param object $copyObj An object of Assignment (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTeacherId($this->getTeacherId());
        $copyObj->setCourseId($this->getCourseId());
        $copyObj->setAssignmentCategoryId($this->getAssignmentCategoryId());
        $copyObj->setName($this->getName());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setMaxPoints($this->getMaxPoints());
        $copyObj->setDueAt($this->getDueAt());

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
     * @return Assignment Clone of current object.
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
     * @return AssignmentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AssignmentPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Teacher object.
     *
     * @param             Teacher $v
     * @return Assignment The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTeacher(Teacher $v = null)
    {
        if ($v === null) {
            $this->setTeacherId(NULL);
        } else {
            $this->setTeacherId($v->getId());
        }

        $this->aTeacher = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Teacher object, it will not be re-added.
        if ($v !== null) {
            $v->addAssignment($this);
        }


        return $this;
    }


    /**
     * Get the associated Teacher object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Teacher The associated Teacher object.
     * @throws PropelException
     */
    public function getTeacher(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aTeacher === null && ($this->teacher_id !== null) && $doQuery) {
            $this->aTeacher = TeacherQuery::create()->findPk($this->teacher_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTeacher->addAssignments($this);
             */
        }

        return $this->aTeacher;
    }

    /**
     * Declares an association between this object and a Course object.
     *
     * @param             Course $v
     * @return Assignment The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCourse(Course $v = null)
    {
        if ($v === null) {
            $this->setCourseId(NULL);
        } else {
            $this->setCourseId($v->getId());
        }

        $this->aCourse = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Course object, it will not be re-added.
        if ($v !== null) {
            $v->addAssignment($this);
        }


        return $this;
    }


    /**
     * Get the associated Course object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Course The associated Course object.
     * @throws PropelException
     */
    public function getCourse(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCourse === null && ($this->course_id !== null) && $doQuery) {
            $this->aCourse = CourseQuery::create()->findPk($this->course_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCourse->addAssignments($this);
             */
        }

        return $this->aCourse;
    }

    /**
     * Declares an association between this object and a AssignmentCategory object.
     *
     * @param             AssignmentCategory $v
     * @return Assignment The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAssignmentCategory(AssignmentCategory $v = null)
    {
        if ($v === null) {
            $this->setAssignmentCategoryId(NULL);
        } else {
            $this->setAssignmentCategoryId($v->getId());
        }

        $this->aAssignmentCategory = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the AssignmentCategory object, it will not be re-added.
        if ($v !== null) {
            $v->addAssignment($this);
        }


        return $this;
    }


    /**
     * Get the associated AssignmentCategory object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return AssignmentCategory The associated AssignmentCategory object.
     * @throws PropelException
     */
    public function getAssignmentCategory(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAssignmentCategory === null && ($this->assignment_category_id !== null) && $doQuery) {
            $this->aAssignmentCategory = AssignmentCategoryQuery::create()->findPk($this->assignment_category_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAssignmentCategory->addAssignments($this);
             */
        }

        return $this->aAssignmentCategory;
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
    }

    /**
     * Clears out the collStudentAssignments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Assignment The current object (for fluent API support)
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
     * If this Assignment is new, it will return
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
                    ->filterByAssignment($this)
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
     * @return Assignment The current object (for fluent API support)
     */
    public function setStudentAssignments(PropelCollection $studentAssignments, PropelPDO $con = null)
    {
        $this->studentAssignmentsScheduledForDeletion = $this->getStudentAssignments(new Criteria(), $con)->diff($studentAssignments);

        foreach ($this->studentAssignmentsScheduledForDeletion as $studentAssignmentRemoved) {
            $studentAssignmentRemoved->setAssignment(null);
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
                ->filterByAssignment($this)
                ->count($con);
        }

        return count($this->collStudentAssignments);
    }

    /**
     * Method called to associate a StudentAssignment object to this object
     * through the StudentAssignment foreign key attribute.
     *
     * @param    StudentAssignment $l StudentAssignment
     * @return Assignment The current object (for fluent API support)
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
        $studentAssignment->setAssignment($this);
    }

    /**
     * @param	StudentAssignment $studentAssignment The studentAssignment object to remove.
     * @return Assignment The current object (for fluent API support)
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
            $studentAssignment->setAssignment(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Assignment is new, it will return
     * an empty collection; or if this Assignment has previously
     * been saved, it will retrieve related StudentAssignments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Assignment.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|StudentAssignment[] List of StudentAssignment objects
     */
    public function getStudentAssignmentsJoinStudent($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = StudentAssignmentQuery::create(null, $criteria);
        $query->joinWith('Student', $join_behavior);

        return $this->getStudentAssignments($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->teacher_id = null;
        $this->course_id = null;
        $this->assignment_category_id = null;
        $this->name = null;
        $this->description = null;
        $this->max_points = null;
        $this->due_at = null;
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
        } // if ($deep)

        if ($this->collStudentAssignments instanceof PropelCollection) {
            $this->collStudentAssignments->clearIterator();
        }
        $this->collStudentAssignments = null;
        $this->aTeacher = null;
        $this->aCourse = null;
        $this->aAssignmentCategory = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(AssignmentPeer::DEFAULT_STRING_FORMAT);
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
