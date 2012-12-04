<?php

namespace Zerebral\BusinessBundle\Model\Course\om;

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
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CoursePeer;
use Zerebral\BusinessBundle\Model\Course\CourseQuery;
use Zerebral\BusinessBundle\Model\Course\CourseStudent;
use Zerebral\BusinessBundle\Model\Course\CourseStudentQuery;
use Zerebral\BusinessBundle\Model\Course\CourseTeacher;
use Zerebral\BusinessBundle\Model\Course\CourseTeacherQuery;
use Zerebral\BusinessBundle\Model\Course\Discipline;
use Zerebral\BusinessBundle\Model\Course\DisciplineQuery;
use Zerebral\BusinessBundle\Model\Course\GradeLevel;
use Zerebral\BusinessBundle\Model\Course\GradeLevelQuery;
use Zerebral\BusinessBundle\Model\User\Teacher;
use Zerebral\BusinessBundle\Model\User\TeacherQuery;

abstract class BaseCourse extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Zerebral\\BusinessBundle\\Model\\Course\\CoursePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CoursePeer
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
     * The value for the discipline_id field.
     * @var        int
     */
    protected $discipline_id;

    /**
     * The value for the grade_level_id field.
     * @var        int
     */
    protected $grade_level_id;

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
     * The value for the created_by field.
     * @var        int
     */
    protected $created_by;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        Teacher
     */
    protected $aTeacher;

    /**
     * @var        Discipline
     */
    protected $aDiscipline;

    /**
     * @var        GradeLevel
     */
    protected $aGradeLevel;

    /**
     * @var        PropelObjectCollection|AssignmentCategory[] Collection to store aggregation of AssignmentCategory objects.
     */
    protected $collAssignmentCategories;
    protected $collAssignmentCategoriesPartial;

    /**
     * @var        PropelObjectCollection|Assignment[] Collection to store aggregation of Assignment objects.
     */
    protected $collAssignments;
    protected $collAssignmentsPartial;

    /**
     * @var        PropelObjectCollection|CourseStudent[] Collection to store aggregation of CourseStudent objects.
     */
    protected $collCourseStudents;
    protected $collCourseStudentsPartial;

    /**
     * @var        PropelObjectCollection|CourseTeacher[] Collection to store aggregation of CourseTeacher objects.
     */
    protected $collCourseTeachers;
    protected $collCourseTeachersPartial;

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
    protected $assignmentCategoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $assignmentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseStudentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseTeachersScheduledForDeletion = null;

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
     * Get the [discipline_id] column value.
     *
     * @return int
     */
    public function getDisciplineId()
    {
        return $this->discipline_id;
    }

    /**
     * Get the [grade_level_id] column value.
     *
     * @return int
     */
    public function getGradeLevelId()
    {
        return $this->grade_level_id;
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
     * Get the [created_by] column value.
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->created_by;
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
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = null)
    {
        if ($this->updated_at === null) {
            return null;
        }

        if ($this->updated_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->updated_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
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
     * @return Course The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CoursePeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [discipline_id] column.
     *
     * @param int $v new value
     * @return Course The current object (for fluent API support)
     */
    public function setDisciplineId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->discipline_id !== $v) {
            $this->discipline_id = $v;
            $this->modifiedColumns[] = CoursePeer::DISCIPLINE_ID;
        }

        if ($this->aDiscipline !== null && $this->aDiscipline->getId() !== $v) {
            $this->aDiscipline = null;
        }


        return $this;
    } // setDisciplineId()

    /**
     * Set the value of [grade_level_id] column.
     *
     * @param int $v new value
     * @return Course The current object (for fluent API support)
     */
    public function setGradeLevelId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->grade_level_id !== $v) {
            $this->grade_level_id = $v;
            $this->modifiedColumns[] = CoursePeer::GRADE_LEVEL_ID;
        }

        if ($this->aGradeLevel !== null && $this->aGradeLevel->getId() !== $v) {
            $this->aGradeLevel = null;
        }


        return $this;
    } // setGradeLevelId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return Course The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = CoursePeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [description] column.
     *
     * @param string $v new value
     * @return Course The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CoursePeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Set the value of [created_by] column.
     *
     * @param int $v new value
     * @return Course The current object (for fluent API support)
     */
    public function setCreatedBy($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->created_by !== $v) {
            $this->created_by = $v;
            $this->modifiedColumns[] = CoursePeer::CREATED_BY;
        }

        if ($this->aTeacher !== null && $this->aTeacher->getId() !== $v) {
            $this->aTeacher = null;
        }


        return $this;
    } // setCreatedBy()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Course The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = CoursePeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Course The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = CoursePeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

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
            $this->discipline_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->grade_level_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->description = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->created_by = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->created_at = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->updated_at = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 8; // 8 = CoursePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Course object", $e);
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

        if ($this->aDiscipline !== null && $this->discipline_id !== $this->aDiscipline->getId()) {
            $this->aDiscipline = null;
        }
        if ($this->aGradeLevel !== null && $this->grade_level_id !== $this->aGradeLevel->getId()) {
            $this->aGradeLevel = null;
        }
        if ($this->aTeacher !== null && $this->created_by !== $this->aTeacher->getId()) {
            $this->aTeacher = null;
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
            $con = Propel::getConnection(CoursePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CoursePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aTeacher = null;
            $this->aDiscipline = null;
            $this->aGradeLevel = null;
            $this->collAssignmentCategories = null;

            $this->collAssignments = null;

            $this->collCourseStudents = null;

            $this->collCourseTeachers = null;

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
            $con = Propel::getConnection(CoursePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CourseQuery::create()
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
            $con = Propel::getConnection(CoursePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CoursePeer::addInstanceToPool($this);
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

            if ($this->aDiscipline !== null) {
                if ($this->aDiscipline->isModified() || $this->aDiscipline->isNew()) {
                    $affectedRows += $this->aDiscipline->save($con);
                }
                $this->setDiscipline($this->aDiscipline);
            }

            if ($this->aGradeLevel !== null) {
                if ($this->aGradeLevel->isModified() || $this->aGradeLevel->isNew()) {
                    $affectedRows += $this->aGradeLevel->save($con);
                }
                $this->setGradeLevel($this->aGradeLevel);
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

            if ($this->assignmentCategoriesScheduledForDeletion !== null) {
                if (!$this->assignmentCategoriesScheduledForDeletion->isEmpty()) {
                    foreach ($this->assignmentCategoriesScheduledForDeletion as $assignmentCategory) {
                        // need to save related object because we set the relation to null
                        $assignmentCategory->save($con);
                    }
                    $this->assignmentCategoriesScheduledForDeletion = null;
                }
            }

            if ($this->collAssignmentCategories !== null) {
                foreach ($this->collAssignmentCategories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->assignmentsScheduledForDeletion !== null) {
                if (!$this->assignmentsScheduledForDeletion->isEmpty()) {
                    AssignmentQuery::create()
                        ->filterByPrimaryKeys($this->assignmentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->assignmentsScheduledForDeletion = null;
                }
            }

            if ($this->collAssignments !== null) {
                foreach ($this->collAssignments as $referrerFK) {
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

            if ($this->courseTeachersScheduledForDeletion !== null) {
                if (!$this->courseTeachersScheduledForDeletion->isEmpty()) {
                    CourseTeacherQuery::create()
                        ->filterByPrimaryKeys($this->courseTeachersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->courseTeachersScheduledForDeletion = null;
                }
            }

            if ($this->collCourseTeachers !== null) {
                foreach ($this->collCourseTeachers as $referrerFK) {
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

        $this->modifiedColumns[] = CoursePeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CoursePeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CoursePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CoursePeer::DISCIPLINE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`discipline_id`';
        }
        if ($this->isColumnModified(CoursePeer::GRADE_LEVEL_ID)) {
            $modifiedColumns[':p' . $index++]  = '`grade_level_id`';
        }
        if ($this->isColumnModified(CoursePeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`name`';
        }
        if ($this->isColumnModified(CoursePeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '`description`';
        }
        if ($this->isColumnModified(CoursePeer::CREATED_BY)) {
            $modifiedColumns[':p' . $index++]  = '`created_by`';
        }
        if ($this->isColumnModified(CoursePeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(CoursePeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `courses` (%s) VALUES (%s)',
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
                    case '`discipline_id`':
                        $stmt->bindValue($identifier, $this->discipline_id, PDO::PARAM_INT);
                        break;
                    case '`grade_level_id`':
                        $stmt->bindValue($identifier, $this->grade_level_id, PDO::PARAM_INT);
                        break;
                    case '`name`':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '`description`':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '`created_by`':
                        $stmt->bindValue($identifier, $this->created_by, PDO::PARAM_INT);
                        break;
                    case '`created_at`':
                        $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
                        break;
                    case '`updated_at`':
                        $stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
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

            if ($this->aDiscipline !== null) {
                if (!$this->aDiscipline->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aDiscipline->getValidationFailures());
                }
            }

            if ($this->aGradeLevel !== null) {
                if (!$this->aGradeLevel->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aGradeLevel->getValidationFailures());
                }
            }


            if (($retval = CoursePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collAssignmentCategories !== null) {
                    foreach ($this->collAssignmentCategories as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collAssignments !== null) {
                    foreach ($this->collAssignments as $referrerFK) {
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

                if ($this->collCourseTeachers !== null) {
                    foreach ($this->collCourseTeachers as $referrerFK) {
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
        $pos = CoursePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDisciplineId();
                break;
            case 2:
                return $this->getGradeLevelId();
                break;
            case 3:
                return $this->getName();
                break;
            case 4:
                return $this->getDescription();
                break;
            case 5:
                return $this->getCreatedBy();
                break;
            case 6:
                return $this->getCreatedAt();
                break;
            case 7:
                return $this->getUpdatedAt();
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
        if (isset($alreadyDumpedObjects['Course'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Course'][$this->getPrimaryKey()] = true;
        $keys = CoursePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDisciplineId(),
            $keys[2] => $this->getGradeLevelId(),
            $keys[3] => $this->getName(),
            $keys[4] => $this->getDescription(),
            $keys[5] => $this->getCreatedBy(),
            $keys[6] => $this->getCreatedAt(),
            $keys[7] => $this->getUpdatedAt(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aTeacher) {
                $result['Teacher'] = $this->aTeacher->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aDiscipline) {
                $result['Discipline'] = $this->aDiscipline->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aGradeLevel) {
                $result['GradeLevel'] = $this->aGradeLevel->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collAssignmentCategories) {
                $result['AssignmentCategories'] = $this->collAssignmentCategories->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAssignments) {
                $result['Assignments'] = $this->collAssignments->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseStudents) {
                $result['CourseStudents'] = $this->collCourseStudents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseTeachers) {
                $result['CourseTeachers'] = $this->collCourseTeachers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CoursePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDisciplineId($value);
                break;
            case 2:
                $this->setGradeLevelId($value);
                break;
            case 3:
                $this->setName($value);
                break;
            case 4:
                $this->setDescription($value);
                break;
            case 5:
                $this->setCreatedBy($value);
                break;
            case 6:
                $this->setCreatedAt($value);
                break;
            case 7:
                $this->setUpdatedAt($value);
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
        $keys = CoursePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDisciplineId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setGradeLevelId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setName($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setDescription($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCreatedBy($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCreatedAt($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setUpdatedAt($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CoursePeer::DATABASE_NAME);

        if ($this->isColumnModified(CoursePeer::ID)) $criteria->add(CoursePeer::ID, $this->id);
        if ($this->isColumnModified(CoursePeer::DISCIPLINE_ID)) $criteria->add(CoursePeer::DISCIPLINE_ID, $this->discipline_id);
        if ($this->isColumnModified(CoursePeer::GRADE_LEVEL_ID)) $criteria->add(CoursePeer::GRADE_LEVEL_ID, $this->grade_level_id);
        if ($this->isColumnModified(CoursePeer::NAME)) $criteria->add(CoursePeer::NAME, $this->name);
        if ($this->isColumnModified(CoursePeer::DESCRIPTION)) $criteria->add(CoursePeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CoursePeer::CREATED_BY)) $criteria->add(CoursePeer::CREATED_BY, $this->created_by);
        if ($this->isColumnModified(CoursePeer::CREATED_AT)) $criteria->add(CoursePeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(CoursePeer::UPDATED_AT)) $criteria->add(CoursePeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(CoursePeer::DATABASE_NAME);
        $criteria->add(CoursePeer::ID, $this->id);

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
     * @param object $copyObj An object of Course (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDisciplineId($this->getDisciplineId());
        $copyObj->setGradeLevelId($this->getGradeLevelId());
        $copyObj->setName($this->getName());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setCreatedBy($this->getCreatedBy());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getAssignmentCategories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAssignmentCategory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAssignments() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAssignment($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCourseStudents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseStudent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCourseTeachers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseTeacher($relObj->copy($deepCopy));
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
     * @return Course Clone of current object.
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
     * @return CoursePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CoursePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Teacher object.
     *
     * @param             Teacher $v
     * @return Course The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTeacher(Teacher $v = null)
    {
        if ($v === null) {
            $this->setCreatedBy(NULL);
        } else {
            $this->setCreatedBy($v->getId());
        }

        $this->aTeacher = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Teacher object, it will not be re-added.
        if ($v !== null) {
            $v->addCourse($this);
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
        if ($this->aTeacher === null && ($this->created_by !== null) && $doQuery) {
            $this->aTeacher = TeacherQuery::create()->findPk($this->created_by, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTeacher->addCourses($this);
             */
        }

        return $this->aTeacher;
    }

    /**
     * Declares an association between this object and a Discipline object.
     *
     * @param             Discipline $v
     * @return Course The current object (for fluent API support)
     * @throws PropelException
     */
    public function setDiscipline(Discipline $v = null)
    {
        if ($v === null) {
            $this->setDisciplineId(NULL);
        } else {
            $this->setDisciplineId($v->getId());
        }

        $this->aDiscipline = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Discipline object, it will not be re-added.
        if ($v !== null) {
            $v->addCourse($this);
        }


        return $this;
    }


    /**
     * Get the associated Discipline object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Discipline The associated Discipline object.
     * @throws PropelException
     */
    public function getDiscipline(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aDiscipline === null && ($this->discipline_id !== null) && $doQuery) {
            $this->aDiscipline = DisciplineQuery::create()->findPk($this->discipline_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aDiscipline->addCourses($this);
             */
        }

        return $this->aDiscipline;
    }

    /**
     * Declares an association between this object and a GradeLevel object.
     *
     * @param             GradeLevel $v
     * @return Course The current object (for fluent API support)
     * @throws PropelException
     */
    public function setGradeLevel(GradeLevel $v = null)
    {
        if ($v === null) {
            $this->setGradeLevelId(NULL);
        } else {
            $this->setGradeLevelId($v->getId());
        }

        $this->aGradeLevel = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the GradeLevel object, it will not be re-added.
        if ($v !== null) {
            $v->addCourse($this);
        }


        return $this;
    }


    /**
     * Get the associated GradeLevel object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return GradeLevel The associated GradeLevel object.
     * @throws PropelException
     */
    public function getGradeLevel(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aGradeLevel === null && ($this->grade_level_id !== null) && $doQuery) {
            $this->aGradeLevel = GradeLevelQuery::create()->findPk($this->grade_level_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aGradeLevel->addCourses($this);
             */
        }

        return $this->aGradeLevel;
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
        if ('AssignmentCategory' == $relationName) {
            $this->initAssignmentCategories();
        }
        if ('Assignment' == $relationName) {
            $this->initAssignments();
        }
        if ('CourseStudent' == $relationName) {
            $this->initCourseStudents();
        }
        if ('CourseTeacher' == $relationName) {
            $this->initCourseTeachers();
        }
    }

    /**
     * Clears out the collAssignmentCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addAssignmentCategories()
     */
    public function clearAssignmentCategories()
    {
        $this->collAssignmentCategories = null; // important to set this to null since that means it is uninitialized
        $this->collAssignmentCategoriesPartial = null;

        return $this;
    }

    /**
     * reset is the collAssignmentCategories collection loaded partially
     *
     * @return void
     */
    public function resetPartialAssignmentCategories($v = true)
    {
        $this->collAssignmentCategoriesPartial = $v;
    }

    /**
     * Initializes the collAssignmentCategories collection.
     *
     * By default this just sets the collAssignmentCategories collection to an empty array (like clearcollAssignmentCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAssignmentCategories($overrideExisting = true)
    {
        if (null !== $this->collAssignmentCategories && !$overrideExisting) {
            return;
        }
        $this->collAssignmentCategories = new PropelObjectCollection();
        $this->collAssignmentCategories->setModel('AssignmentCategory');
    }

    /**
     * Gets an array of AssignmentCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|AssignmentCategory[] List of AssignmentCategory objects
     * @throws PropelException
     */
    public function getAssignmentCategories($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAssignmentCategoriesPartial && !$this->isNew();
        if (null === $this->collAssignmentCategories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAssignmentCategories) {
                // return empty collection
                $this->initAssignmentCategories();
            } else {
                $collAssignmentCategories = AssignmentCategoryQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAssignmentCategoriesPartial && count($collAssignmentCategories)) {
                      $this->initAssignmentCategories(false);

                      foreach($collAssignmentCategories as $obj) {
                        if (false == $this->collAssignmentCategories->contains($obj)) {
                          $this->collAssignmentCategories->append($obj);
                        }
                      }

                      $this->collAssignmentCategoriesPartial = true;
                    }

                    return $collAssignmentCategories;
                }

                if($partial && $this->collAssignmentCategories) {
                    foreach($this->collAssignmentCategories as $obj) {
                        if($obj->isNew()) {
                            $collAssignmentCategories[] = $obj;
                        }
                    }
                }

                $this->collAssignmentCategories = $collAssignmentCategories;
                $this->collAssignmentCategoriesPartial = false;
            }
        }

        return $this->collAssignmentCategories;
    }

    /**
     * Sets a collection of AssignmentCategory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $assignmentCategories A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setAssignmentCategories(PropelCollection $assignmentCategories, PropelPDO $con = null)
    {
        $this->assignmentCategoriesScheduledForDeletion = $this->getAssignmentCategories(new Criteria(), $con)->diff($assignmentCategories);

        foreach ($this->assignmentCategoriesScheduledForDeletion as $assignmentCategoryRemoved) {
            $assignmentCategoryRemoved->setCourse(null);
        }

        $this->collAssignmentCategories = null;
        foreach ($assignmentCategories as $assignmentCategory) {
            $this->addAssignmentCategory($assignmentCategory);
        }

        $this->collAssignmentCategories = $assignmentCategories;
        $this->collAssignmentCategoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AssignmentCategory objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related AssignmentCategory objects.
     * @throws PropelException
     */
    public function countAssignmentCategories(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAssignmentCategoriesPartial && !$this->isNew();
        if (null === $this->collAssignmentCategories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAssignmentCategories) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getAssignmentCategories());
            }
            $query = AssignmentCategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collAssignmentCategories);
    }

    /**
     * Method called to associate a AssignmentCategory object to this object
     * through the AssignmentCategory foreign key attribute.
     *
     * @param    AssignmentCategory $l AssignmentCategory
     * @return Course The current object (for fluent API support)
     */
    public function addAssignmentCategory(AssignmentCategory $l)
    {
        if ($this->collAssignmentCategories === null) {
            $this->initAssignmentCategories();
            $this->collAssignmentCategoriesPartial = true;
        }
        if (!in_array($l, $this->collAssignmentCategories->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAssignmentCategory($l);
        }

        return $this;
    }

    /**
     * @param	AssignmentCategory $assignmentCategory The assignmentCategory object to add.
     */
    protected function doAddAssignmentCategory($assignmentCategory)
    {
        $this->collAssignmentCategories[]= $assignmentCategory;
        $assignmentCategory->setCourse($this);
    }

    /**
     * @param	AssignmentCategory $assignmentCategory The assignmentCategory object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeAssignmentCategory($assignmentCategory)
    {
        if ($this->getAssignmentCategories()->contains($assignmentCategory)) {
            $this->collAssignmentCategories->remove($this->collAssignmentCategories->search($assignmentCategory));
            if (null === $this->assignmentCategoriesScheduledForDeletion) {
                $this->assignmentCategoriesScheduledForDeletion = clone $this->collAssignmentCategories;
                $this->assignmentCategoriesScheduledForDeletion->clear();
            }
            $this->assignmentCategoriesScheduledForDeletion[]= $assignmentCategory;
            $assignmentCategory->setCourse(null);
        }

        return $this;
    }

    /**
     * Clears out the collAssignments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addAssignments()
     */
    public function clearAssignments()
    {
        $this->collAssignments = null; // important to set this to null since that means it is uninitialized
        $this->collAssignmentsPartial = null;

        return $this;
    }

    /**
     * reset is the collAssignments collection loaded partially
     *
     * @return void
     */
    public function resetPartialAssignments($v = true)
    {
        $this->collAssignmentsPartial = $v;
    }

    /**
     * Initializes the collAssignments collection.
     *
     * By default this just sets the collAssignments collection to an empty array (like clearcollAssignments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAssignments($overrideExisting = true)
    {
        if (null !== $this->collAssignments && !$overrideExisting) {
            return;
        }
        $this->collAssignments = new PropelObjectCollection();
        $this->collAssignments->setModel('Assignment');
    }

    /**
     * Gets an array of Assignment objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Assignment[] List of Assignment objects
     * @throws PropelException
     */
    public function getAssignments($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAssignmentsPartial && !$this->isNew();
        if (null === $this->collAssignments || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAssignments) {
                // return empty collection
                $this->initAssignments();
            } else {
                $collAssignments = AssignmentQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAssignmentsPartial && count($collAssignments)) {
                      $this->initAssignments(false);

                      foreach($collAssignments as $obj) {
                        if (false == $this->collAssignments->contains($obj)) {
                          $this->collAssignments->append($obj);
                        }
                      }

                      $this->collAssignmentsPartial = true;
                    }

                    return $collAssignments;
                }

                if($partial && $this->collAssignments) {
                    foreach($this->collAssignments as $obj) {
                        if($obj->isNew()) {
                            $collAssignments[] = $obj;
                        }
                    }
                }

                $this->collAssignments = $collAssignments;
                $this->collAssignmentsPartial = false;
            }
        }

        return $this->collAssignments;
    }

    /**
     * Sets a collection of Assignment objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $assignments A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setAssignments(PropelCollection $assignments, PropelPDO $con = null)
    {
        $this->assignmentsScheduledForDeletion = $this->getAssignments(new Criteria(), $con)->diff($assignments);

        foreach ($this->assignmentsScheduledForDeletion as $assignmentRemoved) {
            $assignmentRemoved->setCourse(null);
        }

        $this->collAssignments = null;
        foreach ($assignments as $assignment) {
            $this->addAssignment($assignment);
        }

        $this->collAssignments = $assignments;
        $this->collAssignmentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Assignment objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Assignment objects.
     * @throws PropelException
     */
    public function countAssignments(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAssignmentsPartial && !$this->isNew();
        if (null === $this->collAssignments || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAssignments) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getAssignments());
            }
            $query = AssignmentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collAssignments);
    }

    /**
     * Method called to associate a Assignment object to this object
     * through the Assignment foreign key attribute.
     *
     * @param    Assignment $l Assignment
     * @return Course The current object (for fluent API support)
     */
    public function addAssignment(Assignment $l)
    {
        if ($this->collAssignments === null) {
            $this->initAssignments();
            $this->collAssignmentsPartial = true;
        }
        if (!in_array($l, $this->collAssignments->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAssignment($l);
        }

        return $this;
    }

    /**
     * @param	Assignment $assignment The assignment object to add.
     */
    protected function doAddAssignment($assignment)
    {
        $this->collAssignments[]= $assignment;
        $assignment->setCourse($this);
    }

    /**
     * @param	Assignment $assignment The assignment object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeAssignment($assignment)
    {
        if ($this->getAssignments()->contains($assignment)) {
            $this->collAssignments->remove($this->collAssignments->search($assignment));
            if (null === $this->assignmentsScheduledForDeletion) {
                $this->assignmentsScheduledForDeletion = clone $this->collAssignments;
                $this->assignmentsScheduledForDeletion->clear();
            }
            $this->assignmentsScheduledForDeletion[]= $assignment;
            $assignment->setCourse(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related Assignments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Assignment[] List of Assignment objects
     */
    public function getAssignmentsJoinTeacher($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AssignmentQuery::create(null, $criteria);
        $query->joinWith('Teacher', $join_behavior);

        return $this->getAssignments($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related Assignments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Assignment[] List of Assignment objects
     */
    public function getAssignmentsJoinAssignmentCategory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AssignmentQuery::create(null, $criteria);
        $query->joinWith('AssignmentCategory', $join_behavior);

        return $this->getAssignments($query, $con);
    }

    /**
     * Clears out the collCourseStudents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
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
     * If this Course is new, it will return
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
                    ->filterByCourse($this)
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
     * @return Course The current object (for fluent API support)
     */
    public function setCourseStudents(PropelCollection $courseStudents, PropelPDO $con = null)
    {
        $this->courseStudentsScheduledForDeletion = $this->getCourseStudents(new Criteria(), $con)->diff($courseStudents);

        foreach ($this->courseStudentsScheduledForDeletion as $courseStudentRemoved) {
            $courseStudentRemoved->setCourse(null);
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
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collCourseStudents);
    }

    /**
     * Method called to associate a CourseStudent object to this object
     * through the CourseStudent foreign key attribute.
     *
     * @param    CourseStudent $l CourseStudent
     * @return Course The current object (for fluent API support)
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
        $courseStudent->setCourse($this);
    }

    /**
     * @param	CourseStudent $courseStudent The courseStudent object to remove.
     * @return Course The current object (for fluent API support)
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
            $courseStudent->setCourse(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related CourseStudents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CourseStudent[] List of CourseStudent objects
     */
    public function getCourseStudentsJoinStudent($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseStudentQuery::create(null, $criteria);
        $query->joinWith('Student', $join_behavior);

        return $this->getCourseStudents($query, $con);
    }

    /**
     * Clears out the collCourseTeachers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addCourseTeachers()
     */
    public function clearCourseTeachers()
    {
        $this->collCourseTeachers = null; // important to set this to null since that means it is uninitialized
        $this->collCourseTeachersPartial = null;

        return $this;
    }

    /**
     * reset is the collCourseTeachers collection loaded partially
     *
     * @return void
     */
    public function resetPartialCourseTeachers($v = true)
    {
        $this->collCourseTeachersPartial = $v;
    }

    /**
     * Initializes the collCourseTeachers collection.
     *
     * By default this just sets the collCourseTeachers collection to an empty array (like clearcollCourseTeachers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCourseTeachers($overrideExisting = true)
    {
        if (null !== $this->collCourseTeachers && !$overrideExisting) {
            return;
        }
        $this->collCourseTeachers = new PropelObjectCollection();
        $this->collCourseTeachers->setModel('CourseTeacher');
    }

    /**
     * Gets an array of CourseTeacher objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CourseTeacher[] List of CourseTeacher objects
     * @throws PropelException
     */
    public function getCourseTeachers($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCourseTeachersPartial && !$this->isNew();
        if (null === $this->collCourseTeachers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCourseTeachers) {
                // return empty collection
                $this->initCourseTeachers();
            } else {
                $collCourseTeachers = CourseTeacherQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCourseTeachersPartial && count($collCourseTeachers)) {
                      $this->initCourseTeachers(false);

                      foreach($collCourseTeachers as $obj) {
                        if (false == $this->collCourseTeachers->contains($obj)) {
                          $this->collCourseTeachers->append($obj);
                        }
                      }

                      $this->collCourseTeachersPartial = true;
                    }

                    return $collCourseTeachers;
                }

                if($partial && $this->collCourseTeachers) {
                    foreach($this->collCourseTeachers as $obj) {
                        if($obj->isNew()) {
                            $collCourseTeachers[] = $obj;
                        }
                    }
                }

                $this->collCourseTeachers = $collCourseTeachers;
                $this->collCourseTeachersPartial = false;
            }
        }

        return $this->collCourseTeachers;
    }

    /**
     * Sets a collection of CourseTeacher objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $courseTeachers A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setCourseTeachers(PropelCollection $courseTeachers, PropelPDO $con = null)
    {
        $this->courseTeachersScheduledForDeletion = $this->getCourseTeachers(new Criteria(), $con)->diff($courseTeachers);

        foreach ($this->courseTeachersScheduledForDeletion as $courseTeacherRemoved) {
            $courseTeacherRemoved->setCourse(null);
        }

        $this->collCourseTeachers = null;
        foreach ($courseTeachers as $courseTeacher) {
            $this->addCourseTeacher($courseTeacher);
        }

        $this->collCourseTeachers = $courseTeachers;
        $this->collCourseTeachersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CourseTeacher objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CourseTeacher objects.
     * @throws PropelException
     */
    public function countCourseTeachers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCourseTeachersPartial && !$this->isNew();
        if (null === $this->collCourseTeachers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCourseTeachers) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getCourseTeachers());
            }
            $query = CourseTeacherQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collCourseTeachers);
    }

    /**
     * Method called to associate a CourseTeacher object to this object
     * through the CourseTeacher foreign key attribute.
     *
     * @param    CourseTeacher $l CourseTeacher
     * @return Course The current object (for fluent API support)
     */
    public function addCourseTeacher(CourseTeacher $l)
    {
        if ($this->collCourseTeachers === null) {
            $this->initCourseTeachers();
            $this->collCourseTeachersPartial = true;
        }
        if (!in_array($l, $this->collCourseTeachers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCourseTeacher($l);
        }

        return $this;
    }

    /**
     * @param	CourseTeacher $courseTeacher The courseTeacher object to add.
     */
    protected function doAddCourseTeacher($courseTeacher)
    {
        $this->collCourseTeachers[]= $courseTeacher;
        $courseTeacher->setCourse($this);
    }

    /**
     * @param	CourseTeacher $courseTeacher The courseTeacher object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeCourseTeacher($courseTeacher)
    {
        if ($this->getCourseTeachers()->contains($courseTeacher)) {
            $this->collCourseTeachers->remove($this->collCourseTeachers->search($courseTeacher));
            if (null === $this->courseTeachersScheduledForDeletion) {
                $this->courseTeachersScheduledForDeletion = clone $this->collCourseTeachers;
                $this->courseTeachersScheduledForDeletion->clear();
            }
            $this->courseTeachersScheduledForDeletion[]= $courseTeacher;
            $courseTeacher->setCourse(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related CourseTeachers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CourseTeacher[] List of CourseTeacher objects
     */
    public function getCourseTeachersJoinTeacher($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseTeacherQuery::create(null, $criteria);
        $query->joinWith('Teacher', $join_behavior);

        return $this->getCourseTeachers($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->discipline_id = null;
        $this->grade_level_id = null;
        $this->name = null;
        $this->description = null;
        $this->created_by = null;
        $this->created_at = null;
        $this->updated_at = null;
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
            if ($this->collAssignmentCategories) {
                foreach ($this->collAssignmentCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAssignments) {
                foreach ($this->collAssignments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourseStudents) {
                foreach ($this->collCourseStudents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourseTeachers) {
                foreach ($this->collCourseTeachers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collAssignmentCategories instanceof PropelCollection) {
            $this->collAssignmentCategories->clearIterator();
        }
        $this->collAssignmentCategories = null;
        if ($this->collAssignments instanceof PropelCollection) {
            $this->collAssignments->clearIterator();
        }
        $this->collAssignments = null;
        if ($this->collCourseStudents instanceof PropelCollection) {
            $this->collCourseStudents->clearIterator();
        }
        $this->collCourseStudents = null;
        if ($this->collCourseTeachers instanceof PropelCollection) {
            $this->collCourseTeachers->clearIterator();
        }
        $this->collCourseTeachers = null;
        $this->aTeacher = null;
        $this->aDiscipline = null;
        $this->aGradeLevel = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CoursePeer::DEFAULT_STRING_FORMAT);
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
