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
use Zerebral\BusinessBundle\Model\Attendance\Attendance;
use Zerebral\BusinessBundle\Model\Attendance\AttendanceQuery;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CoursePeer;
use Zerebral\BusinessBundle\Model\Course\CourseQuery;
use Zerebral\BusinessBundle\Model\Course\CourseScheduleDay;
use Zerebral\BusinessBundle\Model\Course\CourseScheduleDayQuery;
use Zerebral\BusinessBundle\Model\Course\CourseStudent;
use Zerebral\BusinessBundle\Model\Course\CourseStudentQuery;
use Zerebral\BusinessBundle\Model\Course\CourseTeacher;
use Zerebral\BusinessBundle\Model\Course\CourseTeacherQuery;
use Zerebral\BusinessBundle\Model\Course\Discipline;
use Zerebral\BusinessBundle\Model\Course\DisciplineQuery;
use Zerebral\BusinessBundle\Model\Course\GradeLevel;
use Zerebral\BusinessBundle\Model\Course\GradeLevelQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\Feed\FeedItemQuery;
use Zerebral\BusinessBundle\Model\Material\CourseFolder;
use Zerebral\BusinessBundle\Model\Material\CourseFolderQuery;
use Zerebral\BusinessBundle\Model\Material\CourseMaterial;
use Zerebral\BusinessBundle\Model\Material\CourseMaterialQuery;
use Zerebral\BusinessBundle\Model\Notification\Notification;
use Zerebral\BusinessBundle\Model\Notification\NotificationQuery;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\StudentQuery;
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
     * The value for the access_code field.
     * @var        string
     */
    protected $access_code;

    /**
     * The value for the start field.
     * @var        string
     */
    protected $start;

    /**
     * The value for the end field.
     * @var        string
     */
    protected $end;

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
    protected $aCreatedByTeacher;

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
     * @var        PropelObjectCollection|Attendance[] Collection to store aggregation of Attendance objects.
     */
    protected $collAttendances;
    protected $collAttendancesPartial;

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
     * @var        PropelObjectCollection|CourseScheduleDay[] Collection to store aggregation of CourseScheduleDay objects.
     */
    protected $collCourseScheduleDays;
    protected $collCourseScheduleDaysPartial;

    /**
     * @var        PropelObjectCollection|FeedItem[] Collection to store aggregation of FeedItem objects.
     */
    protected $collFeedItems;
    protected $collFeedItemsPartial;

    /**
     * @var        PropelObjectCollection|CourseFolder[] Collection to store aggregation of CourseFolder objects.
     */
    protected $collCourseFolders;
    protected $collCourseFoldersPartial;

    /**
     * @var        PropelObjectCollection|CourseMaterial[] Collection to store aggregation of CourseMaterial objects.
     */
    protected $collCourseMaterials;
    protected $collCourseMaterialsPartial;

    /**
     * @var        PropelObjectCollection|Notification[] Collection to store aggregation of Notification objects.
     */
    protected $collNotifications;
    protected $collNotificationsPartial;

    /**
     * @var        PropelObjectCollection|Student[] Collection to store aggregation of Student objects.
     */
    protected $collStudents;

    /**
     * @var        PropelObjectCollection|Teacher[] Collection to store aggregation of Teacher objects.
     */
    protected $collTeachers;

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
    protected $studentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $teachersScheduledForDeletion = null;

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
    protected $attendancesScheduledForDeletion = null;

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
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseScheduleDaysScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $feedItemsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseFoldersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseMaterialsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $notificationsScheduledForDeletion = null;

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
     * Get the [access_code] column value.
     *
     * @return string
     */
    public function getAccessCode()
    {
        return $this->access_code;
    }

    /**
     * Get the [optionally formatted] temporal [start] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getStart($format = null)
    {
        if ($this->start === null) {
            return null;
        }

        if ($this->start === '0000-00-00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->start);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start, true), $x);
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
     * Get the [optionally formatted] temporal [end] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getEnd($format = null)
    {
        if ($this->end === null) {
            return null;
        }

        if ($this->end === '0000-00-00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->end);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->end, true), $x);
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
        if ($v !== null && is_numeric($v)) {
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
        if ($v !== null && is_numeric($v)) {
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
        if ($v !== null && is_numeric($v)) {
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
        if ($v !== null && is_numeric($v)) {
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
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CoursePeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Set the value of [access_code] column.
     *
     * @param string $v new value
     * @return Course The current object (for fluent API support)
     */
    public function setAccessCode($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->access_code !== $v) {
            $this->access_code = $v;
            $this->modifiedColumns[] = CoursePeer::ACCESS_CODE;
        }


        return $this;
    } // setAccessCode()

    /**
     * Sets the value of [start] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Course The current object (for fluent API support)
     */
    public function setStart($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->start !== null || $dt !== null) {
            $currentDateAsString = ($this->start !== null && $tmpDt = new DateTime($this->start)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->start = $newDateAsString;
                $this->modifiedColumns[] = CoursePeer::START;
            }
        } // if either are not null


        return $this;
    } // setStart()

    /**
     * Sets the value of [end] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Course The current object (for fluent API support)
     */
    public function setEnd($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->end !== null || $dt !== null) {
            $currentDateAsString = ($this->end !== null && $tmpDt = new DateTime($this->end)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->end = $newDateAsString;
                $this->modifiedColumns[] = CoursePeer::END;
            }
        } // if either are not null


        return $this;
    } // setEnd()

    /**
     * Set the value of [created_by] column.
     *
     * @param int $v new value
     * @return Course The current object (for fluent API support)
     */
    public function setCreatedBy($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->created_by !== $v) {
            $this->created_by = $v;
            $this->modifiedColumns[] = CoursePeer::CREATED_BY;
        }

        if ($this->aCreatedByTeacher !== null && $this->aCreatedByTeacher->getId() !== $v) {
            $this->aCreatedByTeacher = null;
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
            $this->access_code = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->start = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->end = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->created_by = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
            $this->created_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->updated_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 11; // 11 = CoursePeer::NUM_HYDRATE_COLUMNS.

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
        if ($this->aCreatedByTeacher !== null && $this->created_by !== $this->aCreatedByTeacher->getId()) {
            $this->aCreatedByTeacher = null;
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

            $this->aCreatedByTeacher = null;
            $this->aDiscipline = null;
            $this->aGradeLevel = null;
            $this->collAssignmentCategories = null;

            $this->collAssignments = null;

            $this->collAttendances = null;

            $this->collCourseStudents = null;

            $this->collCourseTeachers = null;

            $this->collCourseScheduleDays = null;

            $this->collFeedItems = null;

            $this->collCourseFolders = null;

            $this->collCourseMaterials = null;

            $this->collNotifications = null;

            $this->collStudents = null;
            $this->collTeachers = null;
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

            if ($this->aCreatedByTeacher !== null) {
                if ($this->aCreatedByTeacher->isModified() || $this->aCreatedByTeacher->isNew()) {
                    $affectedRows += $this->aCreatedByTeacher->save($con);
                }
                $this->setCreatedByTeacher($this->aCreatedByTeacher);
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

            if ($this->studentsScheduledForDeletion !== null) {
                if (!$this->studentsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->studentsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    CourseStudentQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->studentsScheduledForDeletion = null;
                }

                foreach ($this->getStudents() as $student) {
                    if ($student->isModified()) {
                        $student->save($con);
                    }
                }
            } elseif ($this->collStudents) {
                foreach ($this->collStudents as $student) {
                    if ($student->isModified()) {
                        $student->save($con);
                    }
                }
            }

            if ($this->teachersScheduledForDeletion !== null) {
                if (!$this->teachersScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->teachersScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    CourseTeacherQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->teachersScheduledForDeletion = null;
                }

                foreach ($this->getTeachers() as $teacher) {
                    if ($teacher->isModified()) {
                        $teacher->save($con);
                    }
                }
            } elseif ($this->collTeachers) {
                foreach ($this->collTeachers as $teacher) {
                    if ($teacher->isModified()) {
                        $teacher->save($con);
                    }
                }
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

            if ($this->attendancesScheduledForDeletion !== null) {
                if (!$this->attendancesScheduledForDeletion->isEmpty()) {
                    AttendanceQuery::create()
                        ->filterByPrimaryKeys($this->attendancesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->attendancesScheduledForDeletion = null;
                }
            }

            if ($this->collAttendances !== null) {
                foreach ($this->collAttendances as $referrerFK) {
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

            if ($this->courseScheduleDaysScheduledForDeletion !== null) {
                if (!$this->courseScheduleDaysScheduledForDeletion->isEmpty()) {
                    CourseScheduleDayQuery::create()
                        ->filterByPrimaryKeys($this->courseScheduleDaysScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->courseScheduleDaysScheduledForDeletion = null;
                }
            }

            if ($this->collCourseScheduleDays !== null) {
                foreach ($this->collCourseScheduleDays as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->feedItemsScheduledForDeletion !== null) {
                if (!$this->feedItemsScheduledForDeletion->isEmpty()) {
                    foreach ($this->feedItemsScheduledForDeletion as $feedItem) {
                        // need to save related object because we set the relation to null
                        $feedItem->save($con);
                    }
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

            if ($this->courseFoldersScheduledForDeletion !== null) {
                if (!$this->courseFoldersScheduledForDeletion->isEmpty()) {
                    CourseFolderQuery::create()
                        ->filterByPrimaryKeys($this->courseFoldersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->courseFoldersScheduledForDeletion = null;
                }
            }

            if ($this->collCourseFolders !== null) {
                foreach ($this->collCourseFolders as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->courseMaterialsScheduledForDeletion !== null) {
                if (!$this->courseMaterialsScheduledForDeletion->isEmpty()) {
                    CourseMaterialQuery::create()
                        ->filterByPrimaryKeys($this->courseMaterialsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->courseMaterialsScheduledForDeletion = null;
                }
            }

            if ($this->collCourseMaterials !== null) {
                foreach ($this->collCourseMaterials as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->notificationsScheduledForDeletion !== null) {
                if (!$this->notificationsScheduledForDeletion->isEmpty()) {
                    foreach ($this->notificationsScheduledForDeletion as $notification) {
                        // need to save related object because we set the relation to null
                        $notification->save($con);
                    }
                    $this->notificationsScheduledForDeletion = null;
                }
            }

            if ($this->collNotifications !== null) {
                foreach ($this->collNotifications as $referrerFK) {
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
        if ($this->isColumnModified(CoursePeer::ACCESS_CODE)) {
            $modifiedColumns[':p' . $index++]  = '`access_code`';
        }
        if ($this->isColumnModified(CoursePeer::START)) {
            $modifiedColumns[':p' . $index++]  = '`start`';
        }
        if ($this->isColumnModified(CoursePeer::END)) {
            $modifiedColumns[':p' . $index++]  = '`end`';
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
                    case '`access_code`':
                        $stmt->bindValue($identifier, $this->access_code, PDO::PARAM_STR);
                        break;
                    case '`start`':
                        $stmt->bindValue($identifier, $this->start, PDO::PARAM_STR);
                        break;
                    case '`end`':
                        $stmt->bindValue($identifier, $this->end, PDO::PARAM_STR);
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

            if ($this->aCreatedByTeacher !== null) {
                if (!$this->aCreatedByTeacher->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCreatedByTeacher->getValidationFailures());
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

                if ($this->collAttendances !== null) {
                    foreach ($this->collAttendances as $referrerFK) {
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

                if ($this->collCourseScheduleDays !== null) {
                    foreach ($this->collCourseScheduleDays as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collFeedItems !== null) {
                    foreach ($this->collFeedItems as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCourseFolders !== null) {
                    foreach ($this->collCourseFolders as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCourseMaterials !== null) {
                    foreach ($this->collCourseMaterials as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collNotifications !== null) {
                    foreach ($this->collNotifications as $referrerFK) {
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
                return $this->getAccessCode();
                break;
            case 6:
                return $this->getStart();
                break;
            case 7:
                return $this->getEnd();
                break;
            case 8:
                return $this->getCreatedBy();
                break;
            case 9:
                return $this->getCreatedAt();
                break;
            case 10:
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
            $keys[5] => $this->getAccessCode(),
            $keys[6] => $this->getStart(),
            $keys[7] => $this->getEnd(),
            $keys[8] => $this->getCreatedBy(),
            $keys[9] => $this->getCreatedAt(),
            $keys[10] => $this->getUpdatedAt(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aCreatedByTeacher) {
                $result['CreatedByTeacher'] = $this->aCreatedByTeacher->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
            if (null !== $this->collAttendances) {
                $result['Attendances'] = $this->collAttendances->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseStudents) {
                $result['CourseStudents'] = $this->collCourseStudents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseTeachers) {
                $result['CourseTeachers'] = $this->collCourseTeachers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseScheduleDays) {
                $result['CourseScheduleDays'] = $this->collCourseScheduleDays->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFeedItems) {
                $result['FeedItems'] = $this->collFeedItems->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseFolders) {
                $result['CourseFolders'] = $this->collCourseFolders->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseMaterials) {
                $result['CourseMaterials'] = $this->collCourseMaterials->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collNotifications) {
                $result['Notifications'] = $this->collNotifications->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
                $this->setAccessCode($value);
                break;
            case 6:
                $this->setStart($value);
                break;
            case 7:
                $this->setEnd($value);
                break;
            case 8:
                $this->setCreatedBy($value);
                break;
            case 9:
                $this->setCreatedAt($value);
                break;
            case 10:
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
        if (array_key_exists($keys[5], $arr)) $this->setAccessCode($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setStart($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setEnd($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCreatedBy($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setCreatedAt($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setUpdatedAt($arr[$keys[10]]);
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
        if ($this->isColumnModified(CoursePeer::ACCESS_CODE)) $criteria->add(CoursePeer::ACCESS_CODE, $this->access_code);
        if ($this->isColumnModified(CoursePeer::START)) $criteria->add(CoursePeer::START, $this->start);
        if ($this->isColumnModified(CoursePeer::END)) $criteria->add(CoursePeer::END, $this->end);
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
        $copyObj->setAccessCode($this->getAccessCode());
        $copyObj->setStart($this->getStart());
        $copyObj->setEnd($this->getEnd());
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

            foreach ($this->getAttendances() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAttendance($relObj->copy($deepCopy));
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

            foreach ($this->getCourseScheduleDays() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseScheduleDay($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getFeedItems() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFeedItem($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCourseFolders() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseFolder($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCourseMaterials() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseMaterial($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getNotifications() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNotification($relObj->copy($deepCopy));
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
    public function setCreatedByTeacher(Teacher $v = null)
    {
        if ($v === null) {
            $this->setCreatedBy(NULL);
        } else {
            $this->setCreatedBy($v->getId());
        }

        $this->aCreatedByTeacher = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Teacher object, it will not be re-added.
        if ($v !== null) {
            $v->addCreatedByTeacher($this);
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
    public function getCreatedByTeacher(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCreatedByTeacher === null && ($this->created_by !== null) && $doQuery) {
            $this->aCreatedByTeacher = TeacherQuery::create()->findPk($this->created_by, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCreatedByTeacher->addCreatedByTeachers($this);
             */
        }

        return $this->aCreatedByTeacher;
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
        if ('Attendance' == $relationName) {
            $this->initAttendances();
        }
        if ('CourseStudent' == $relationName) {
            $this->initCourseStudents();
        }
        if ('CourseTeacher' == $relationName) {
            $this->initCourseTeachers();
        }
        if ('CourseScheduleDay' == $relationName) {
            $this->initCourseScheduleDays();
        }
        if ('FeedItem' == $relationName) {
            $this->initFeedItems();
        }
        if ('CourseFolder' == $relationName) {
            $this->initCourseFolders();
        }
        if ('CourseMaterial' == $relationName) {
            $this->initCourseMaterials();
        }
        if ('Notification' == $relationName) {
            $this->initNotifications();
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

                    $collAssignmentCategories->getInternalIterator()->rewind();
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
        $assignmentCategoriesToDelete = $this->getAssignmentCategories(new Criteria(), $con)->diff($assignmentCategories);

        $this->assignmentCategoriesScheduledForDeletion = unserialize(serialize($assignmentCategoriesToDelete));

        foreach ($assignmentCategoriesToDelete as $assignmentCategoryRemoved) {
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
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related AssignmentCategories from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AssignmentCategory[] List of AssignmentCategory objects
     */
    public function getAssignmentCategoriesJoinTeacher($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AssignmentCategoryQuery::create(null, $criteria);
        $query->joinWith('Teacher', $join_behavior);

        return $this->getAssignmentCategories($query, $con);
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

                    $collAssignments->getInternalIterator()->rewind();
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
        $assignmentsToDelete = $this->getAssignments(new Criteria(), $con)->diff($assignments);

        $this->assignmentsScheduledForDeletion = unserialize(serialize($assignmentsToDelete));

        foreach ($assignmentsToDelete as $assignmentRemoved) {
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
            $this->assignmentsScheduledForDeletion[]= clone $assignment;
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
     * Clears out the collAttendances collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addAttendances()
     */
    public function clearAttendances()
    {
        $this->collAttendances = null; // important to set this to null since that means it is uninitialized
        $this->collAttendancesPartial = null;

        return $this;
    }

    /**
     * reset is the collAttendances collection loaded partially
     *
     * @return void
     */
    public function resetPartialAttendances($v = true)
    {
        $this->collAttendancesPartial = $v;
    }

    /**
     * Initializes the collAttendances collection.
     *
     * By default this just sets the collAttendances collection to an empty array (like clearcollAttendances());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAttendances($overrideExisting = true)
    {
        if (null !== $this->collAttendances && !$overrideExisting) {
            return;
        }
        $this->collAttendances = new PropelObjectCollection();
        $this->collAttendances->setModel('Attendance');
    }

    /**
     * Gets an array of Attendance objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Attendance[] List of Attendance objects
     * @throws PropelException
     */
    public function getAttendances($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAttendancesPartial && !$this->isNew();
        if (null === $this->collAttendances || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAttendances) {
                // return empty collection
                $this->initAttendances();
            } else {
                $collAttendances = AttendanceQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAttendancesPartial && count($collAttendances)) {
                      $this->initAttendances(false);

                      foreach($collAttendances as $obj) {
                        if (false == $this->collAttendances->contains($obj)) {
                          $this->collAttendances->append($obj);
                        }
                      }

                      $this->collAttendancesPartial = true;
                    }

                    $collAttendances->getInternalIterator()->rewind();
                    return $collAttendances;
                }

                if($partial && $this->collAttendances) {
                    foreach($this->collAttendances as $obj) {
                        if($obj->isNew()) {
                            $collAttendances[] = $obj;
                        }
                    }
                }

                $this->collAttendances = $collAttendances;
                $this->collAttendancesPartial = false;
            }
        }

        return $this->collAttendances;
    }

    /**
     * Sets a collection of Attendance objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $attendances A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setAttendances(PropelCollection $attendances, PropelPDO $con = null)
    {
        $attendancesToDelete = $this->getAttendances(new Criteria(), $con)->diff($attendances);

        $this->attendancesScheduledForDeletion = unserialize(serialize($attendancesToDelete));

        foreach ($attendancesToDelete as $attendanceRemoved) {
            $attendanceRemoved->setCourse(null);
        }

        $this->collAttendances = null;
        foreach ($attendances as $attendance) {
            $this->addAttendance($attendance);
        }

        $this->collAttendances = $attendances;
        $this->collAttendancesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Attendance objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Attendance objects.
     * @throws PropelException
     */
    public function countAttendances(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAttendancesPartial && !$this->isNew();
        if (null === $this->collAttendances || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAttendances) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getAttendances());
            }
            $query = AttendanceQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collAttendances);
    }

    /**
     * Method called to associate a Attendance object to this object
     * through the Attendance foreign key attribute.
     *
     * @param    Attendance $l Attendance
     * @return Course The current object (for fluent API support)
     */
    public function addAttendance(Attendance $l)
    {
        if ($this->collAttendances === null) {
            $this->initAttendances();
            $this->collAttendancesPartial = true;
        }
        if (!in_array($l, $this->collAttendances->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAttendance($l);
        }

        return $this;
    }

    /**
     * @param	Attendance $attendance The attendance object to add.
     */
    protected function doAddAttendance($attendance)
    {
        $this->collAttendances[]= $attendance;
        $attendance->setCourse($this);
    }

    /**
     * @param	Attendance $attendance The attendance object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeAttendance($attendance)
    {
        if ($this->getAttendances()->contains($attendance)) {
            $this->collAttendances->remove($this->collAttendances->search($attendance));
            if (null === $this->attendancesScheduledForDeletion) {
                $this->attendancesScheduledForDeletion = clone $this->collAttendances;
                $this->attendancesScheduledForDeletion->clear();
            }
            $this->attendancesScheduledForDeletion[]= clone $attendance;
            $attendance->setCourse(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related Attendances from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Attendance[] List of Attendance objects
     */
    public function getAttendancesJoinTeacher($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AttendanceQuery::create(null, $criteria);
        $query->joinWith('Teacher', $join_behavior);

        return $this->getAttendances($query, $con);
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

                    $collCourseStudents->getInternalIterator()->rewind();
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
        $courseStudentsToDelete = $this->getCourseStudents(new Criteria(), $con)->diff($courseStudents);

        $this->courseStudentsScheduledForDeletion = unserialize(serialize($courseStudentsToDelete));

        foreach ($courseStudentsToDelete as $courseStudentRemoved) {
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
            $this->courseStudentsScheduledForDeletion[]= clone $courseStudent;
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

                    $collCourseTeachers->getInternalIterator()->rewind();
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
        $courseTeachersToDelete = $this->getCourseTeachers(new Criteria(), $con)->diff($courseTeachers);

        $this->courseTeachersScheduledForDeletion = unserialize(serialize($courseTeachersToDelete));

        foreach ($courseTeachersToDelete as $courseTeacherRemoved) {
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
            $this->courseTeachersScheduledForDeletion[]= clone $courseTeacher;
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
     * Clears out the collCourseScheduleDays collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addCourseScheduleDays()
     */
    public function clearCourseScheduleDays()
    {
        $this->collCourseScheduleDays = null; // important to set this to null since that means it is uninitialized
        $this->collCourseScheduleDaysPartial = null;

        return $this;
    }

    /**
     * reset is the collCourseScheduleDays collection loaded partially
     *
     * @return void
     */
    public function resetPartialCourseScheduleDays($v = true)
    {
        $this->collCourseScheduleDaysPartial = $v;
    }

    /**
     * Initializes the collCourseScheduleDays collection.
     *
     * By default this just sets the collCourseScheduleDays collection to an empty array (like clearcollCourseScheduleDays());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCourseScheduleDays($overrideExisting = true)
    {
        if (null !== $this->collCourseScheduleDays && !$overrideExisting) {
            return;
        }
        $this->collCourseScheduleDays = new PropelObjectCollection();
        $this->collCourseScheduleDays->setModel('CourseScheduleDay');
    }

    /**
     * Gets an array of CourseScheduleDay objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CourseScheduleDay[] List of CourseScheduleDay objects
     * @throws PropelException
     */
    public function getCourseScheduleDays($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCourseScheduleDaysPartial && !$this->isNew();
        if (null === $this->collCourseScheduleDays || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCourseScheduleDays) {
                // return empty collection
                $this->initCourseScheduleDays();
            } else {
                $collCourseScheduleDays = CourseScheduleDayQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCourseScheduleDaysPartial && count($collCourseScheduleDays)) {
                      $this->initCourseScheduleDays(false);

                      foreach($collCourseScheduleDays as $obj) {
                        if (false == $this->collCourseScheduleDays->contains($obj)) {
                          $this->collCourseScheduleDays->append($obj);
                        }
                      }

                      $this->collCourseScheduleDaysPartial = true;
                    }

                    $collCourseScheduleDays->getInternalIterator()->rewind();
                    return $collCourseScheduleDays;
                }

                if($partial && $this->collCourseScheduleDays) {
                    foreach($this->collCourseScheduleDays as $obj) {
                        if($obj->isNew()) {
                            $collCourseScheduleDays[] = $obj;
                        }
                    }
                }

                $this->collCourseScheduleDays = $collCourseScheduleDays;
                $this->collCourseScheduleDaysPartial = false;
            }
        }

        return $this->collCourseScheduleDays;
    }

    /**
     * Sets a collection of CourseScheduleDay objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $courseScheduleDays A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setCourseScheduleDays(PropelCollection $courseScheduleDays, PropelPDO $con = null)
    {
        $courseScheduleDaysToDelete = $this->getCourseScheduleDays(new Criteria(), $con)->diff($courseScheduleDays);

        $this->courseScheduleDaysScheduledForDeletion = unserialize(serialize($courseScheduleDaysToDelete));

        foreach ($courseScheduleDaysToDelete as $courseScheduleDayRemoved) {
            $courseScheduleDayRemoved->setCourse(null);
        }

        $this->collCourseScheduleDays = null;
        foreach ($courseScheduleDays as $courseScheduleDay) {
            $this->addCourseScheduleDay($courseScheduleDay);
        }

        $this->collCourseScheduleDays = $courseScheduleDays;
        $this->collCourseScheduleDaysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CourseScheduleDay objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CourseScheduleDay objects.
     * @throws PropelException
     */
    public function countCourseScheduleDays(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCourseScheduleDaysPartial && !$this->isNew();
        if (null === $this->collCourseScheduleDays || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCourseScheduleDays) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getCourseScheduleDays());
            }
            $query = CourseScheduleDayQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collCourseScheduleDays);
    }

    /**
     * Method called to associate a CourseScheduleDay object to this object
     * through the CourseScheduleDay foreign key attribute.
     *
     * @param    CourseScheduleDay $l CourseScheduleDay
     * @return Course The current object (for fluent API support)
     */
    public function addCourseScheduleDay(CourseScheduleDay $l)
    {
        if ($this->collCourseScheduleDays === null) {
            $this->initCourseScheduleDays();
            $this->collCourseScheduleDaysPartial = true;
        }
        if (!in_array($l, $this->collCourseScheduleDays->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCourseScheduleDay($l);
        }

        return $this;
    }

    /**
     * @param	CourseScheduleDay $courseScheduleDay The courseScheduleDay object to add.
     */
    protected function doAddCourseScheduleDay($courseScheduleDay)
    {
        $this->collCourseScheduleDays[]= $courseScheduleDay;
        $courseScheduleDay->setCourse($this);
    }

    /**
     * @param	CourseScheduleDay $courseScheduleDay The courseScheduleDay object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeCourseScheduleDay($courseScheduleDay)
    {
        if ($this->getCourseScheduleDays()->contains($courseScheduleDay)) {
            $this->collCourseScheduleDays->remove($this->collCourseScheduleDays->search($courseScheduleDay));
            if (null === $this->courseScheduleDaysScheduledForDeletion) {
                $this->courseScheduleDaysScheduledForDeletion = clone $this->collCourseScheduleDays;
                $this->courseScheduleDaysScheduledForDeletion->clear();
            }
            $this->courseScheduleDaysScheduledForDeletion[]= clone $courseScheduleDay;
            $courseScheduleDay->setCourse(null);
        }

        return $this;
    }

    /**
     * Clears out the collFeedItems collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
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
     * If this Course is new, it will return
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
                    ->filterByCourse($this)
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

                    $collFeedItems->getInternalIterator()->rewind();
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
     * @return Course The current object (for fluent API support)
     */
    public function setFeedItems(PropelCollection $feedItems, PropelPDO $con = null)
    {
        $feedItemsToDelete = $this->getFeedItems(new Criteria(), $con)->diff($feedItems);

        $this->feedItemsScheduledForDeletion = unserialize(serialize($feedItemsToDelete));

        foreach ($feedItemsToDelete as $feedItemRemoved) {
            $feedItemRemoved->setCourse(null);
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
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collFeedItems);
    }

    /**
     * Method called to associate a FeedItem object to this object
     * through the FeedItem foreign key attribute.
     *
     * @param    FeedItem $l FeedItem
     * @return Course The current object (for fluent API support)
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
        $feedItem->setCourse($this);
    }

    /**
     * @param	FeedItem $feedItem The feedItem object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeFeedItem($feedItem)
    {
        if ($this->getFeedItems()->contains($feedItem)) {
            $this->collFeedItems->remove($this->collFeedItems->search($feedItem));
            if (null === $this->feedItemsScheduledForDeletion) {
                $this->feedItemsScheduledForDeletion = clone $this->collFeedItems;
                $this->feedItemsScheduledForDeletion->clear();
            }
            $this->feedItemsScheduledForDeletion[]= $feedItem;
            $feedItem->setCourse(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FeedItem[] List of FeedItem objects
     */
    public function getFeedItemsJoinFeedContent($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FeedItemQuery::create(null, $criteria);
        $query->joinWith('FeedContent', $join_behavior);

        return $this->getFeedItems($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
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
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
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
     * Clears out the collCourseFolders collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addCourseFolders()
     */
    public function clearCourseFolders()
    {
        $this->collCourseFolders = null; // important to set this to null since that means it is uninitialized
        $this->collCourseFoldersPartial = null;

        return $this;
    }

    /**
     * reset is the collCourseFolders collection loaded partially
     *
     * @return void
     */
    public function resetPartialCourseFolders($v = true)
    {
        $this->collCourseFoldersPartial = $v;
    }

    /**
     * Initializes the collCourseFolders collection.
     *
     * By default this just sets the collCourseFolders collection to an empty array (like clearcollCourseFolders());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCourseFolders($overrideExisting = true)
    {
        if (null !== $this->collCourseFolders && !$overrideExisting) {
            return;
        }
        $this->collCourseFolders = new PropelObjectCollection();
        $this->collCourseFolders->setModel('CourseFolder');
    }

    /**
     * Gets an array of CourseFolder objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CourseFolder[] List of CourseFolder objects
     * @throws PropelException
     */
    public function getCourseFolders($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCourseFoldersPartial && !$this->isNew();
        if (null === $this->collCourseFolders || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCourseFolders) {
                // return empty collection
                $this->initCourseFolders();
            } else {
                $collCourseFolders = CourseFolderQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCourseFoldersPartial && count($collCourseFolders)) {
                      $this->initCourseFolders(false);

                      foreach($collCourseFolders as $obj) {
                        if (false == $this->collCourseFolders->contains($obj)) {
                          $this->collCourseFolders->append($obj);
                        }
                      }

                      $this->collCourseFoldersPartial = true;
                    }

                    $collCourseFolders->getInternalIterator()->rewind();
                    return $collCourseFolders;
                }

                if($partial && $this->collCourseFolders) {
                    foreach($this->collCourseFolders as $obj) {
                        if($obj->isNew()) {
                            $collCourseFolders[] = $obj;
                        }
                    }
                }

                $this->collCourseFolders = $collCourseFolders;
                $this->collCourseFoldersPartial = false;
            }
        }

        return $this->collCourseFolders;
    }

    /**
     * Sets a collection of CourseFolder objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $courseFolders A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setCourseFolders(PropelCollection $courseFolders, PropelPDO $con = null)
    {
        $courseFoldersToDelete = $this->getCourseFolders(new Criteria(), $con)->diff($courseFolders);

        $this->courseFoldersScheduledForDeletion = unserialize(serialize($courseFoldersToDelete));

        foreach ($courseFoldersToDelete as $courseFolderRemoved) {
            $courseFolderRemoved->setCourse(null);
        }

        $this->collCourseFolders = null;
        foreach ($courseFolders as $courseFolder) {
            $this->addCourseFolder($courseFolder);
        }

        $this->collCourseFolders = $courseFolders;
        $this->collCourseFoldersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CourseFolder objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CourseFolder objects.
     * @throws PropelException
     */
    public function countCourseFolders(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCourseFoldersPartial && !$this->isNew();
        if (null === $this->collCourseFolders || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCourseFolders) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getCourseFolders());
            }
            $query = CourseFolderQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collCourseFolders);
    }

    /**
     * Method called to associate a CourseFolder object to this object
     * through the CourseFolder foreign key attribute.
     *
     * @param    CourseFolder $l CourseFolder
     * @return Course The current object (for fluent API support)
     */
    public function addCourseFolder(CourseFolder $l)
    {
        if ($this->collCourseFolders === null) {
            $this->initCourseFolders();
            $this->collCourseFoldersPartial = true;
        }
        if (!in_array($l, $this->collCourseFolders->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCourseFolder($l);
        }

        return $this;
    }

    /**
     * @param	CourseFolder $courseFolder The courseFolder object to add.
     */
    protected function doAddCourseFolder($courseFolder)
    {
        $this->collCourseFolders[]= $courseFolder;
        $courseFolder->setCourse($this);
    }

    /**
     * @param	CourseFolder $courseFolder The courseFolder object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeCourseFolder($courseFolder)
    {
        if ($this->getCourseFolders()->contains($courseFolder)) {
            $this->collCourseFolders->remove($this->collCourseFolders->search($courseFolder));
            if (null === $this->courseFoldersScheduledForDeletion) {
                $this->courseFoldersScheduledForDeletion = clone $this->collCourseFolders;
                $this->courseFoldersScheduledForDeletion->clear();
            }
            $this->courseFoldersScheduledForDeletion[]= clone $courseFolder;
            $courseFolder->setCourse(null);
        }

        return $this;
    }

    /**
     * Clears out the collCourseMaterials collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addCourseMaterials()
     */
    public function clearCourseMaterials()
    {
        $this->collCourseMaterials = null; // important to set this to null since that means it is uninitialized
        $this->collCourseMaterialsPartial = null;

        return $this;
    }

    /**
     * reset is the collCourseMaterials collection loaded partially
     *
     * @return void
     */
    public function resetPartialCourseMaterials($v = true)
    {
        $this->collCourseMaterialsPartial = $v;
    }

    /**
     * Initializes the collCourseMaterials collection.
     *
     * By default this just sets the collCourseMaterials collection to an empty array (like clearcollCourseMaterials());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCourseMaterials($overrideExisting = true)
    {
        if (null !== $this->collCourseMaterials && !$overrideExisting) {
            return;
        }
        $this->collCourseMaterials = new PropelObjectCollection();
        $this->collCourseMaterials->setModel('CourseMaterial');
    }

    /**
     * Gets an array of CourseMaterial objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CourseMaterial[] List of CourseMaterial objects
     * @throws PropelException
     */
    public function getCourseMaterials($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCourseMaterialsPartial && !$this->isNew();
        if (null === $this->collCourseMaterials || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCourseMaterials) {
                // return empty collection
                $this->initCourseMaterials();
            } else {
                $collCourseMaterials = CourseMaterialQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCourseMaterialsPartial && count($collCourseMaterials)) {
                      $this->initCourseMaterials(false);

                      foreach($collCourseMaterials as $obj) {
                        if (false == $this->collCourseMaterials->contains($obj)) {
                          $this->collCourseMaterials->append($obj);
                        }
                      }

                      $this->collCourseMaterialsPartial = true;
                    }

                    $collCourseMaterials->getInternalIterator()->rewind();
                    return $collCourseMaterials;
                }

                if($partial && $this->collCourseMaterials) {
                    foreach($this->collCourseMaterials as $obj) {
                        if($obj->isNew()) {
                            $collCourseMaterials[] = $obj;
                        }
                    }
                }

                $this->collCourseMaterials = $collCourseMaterials;
                $this->collCourseMaterialsPartial = false;
            }
        }

        return $this->collCourseMaterials;
    }

    /**
     * Sets a collection of CourseMaterial objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $courseMaterials A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setCourseMaterials(PropelCollection $courseMaterials, PropelPDO $con = null)
    {
        $courseMaterialsToDelete = $this->getCourseMaterials(new Criteria(), $con)->diff($courseMaterials);

        $this->courseMaterialsScheduledForDeletion = unserialize(serialize($courseMaterialsToDelete));

        foreach ($courseMaterialsToDelete as $courseMaterialRemoved) {
            $courseMaterialRemoved->setCourse(null);
        }

        $this->collCourseMaterials = null;
        foreach ($courseMaterials as $courseMaterial) {
            $this->addCourseMaterial($courseMaterial);
        }

        $this->collCourseMaterials = $courseMaterials;
        $this->collCourseMaterialsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CourseMaterial objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CourseMaterial objects.
     * @throws PropelException
     */
    public function countCourseMaterials(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCourseMaterialsPartial && !$this->isNew();
        if (null === $this->collCourseMaterials || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCourseMaterials) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getCourseMaterials());
            }
            $query = CourseMaterialQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collCourseMaterials);
    }

    /**
     * Method called to associate a CourseMaterial object to this object
     * through the CourseMaterial foreign key attribute.
     *
     * @param    CourseMaterial $l CourseMaterial
     * @return Course The current object (for fluent API support)
     */
    public function addCourseMaterial(CourseMaterial $l)
    {
        if ($this->collCourseMaterials === null) {
            $this->initCourseMaterials();
            $this->collCourseMaterialsPartial = true;
        }
        if (!in_array($l, $this->collCourseMaterials->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCourseMaterial($l);
        }

        return $this;
    }

    /**
     * @param	CourseMaterial $courseMaterial The courseMaterial object to add.
     */
    protected function doAddCourseMaterial($courseMaterial)
    {
        $this->collCourseMaterials[]= $courseMaterial;
        $courseMaterial->setCourse($this);
    }

    /**
     * @param	CourseMaterial $courseMaterial The courseMaterial object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeCourseMaterial($courseMaterial)
    {
        if ($this->getCourseMaterials()->contains($courseMaterial)) {
            $this->collCourseMaterials->remove($this->collCourseMaterials->search($courseMaterial));
            if (null === $this->courseMaterialsScheduledForDeletion) {
                $this->courseMaterialsScheduledForDeletion = clone $this->collCourseMaterials;
                $this->courseMaterialsScheduledForDeletion->clear();
            }
            $this->courseMaterialsScheduledForDeletion[]= clone $courseMaterial;
            $courseMaterial->setCourse(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related CourseMaterials from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CourseMaterial[] List of CourseMaterial objects
     */
    public function getCourseMaterialsJoinTeacher($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseMaterialQuery::create(null, $criteria);
        $query->joinWith('Teacher', $join_behavior);

        return $this->getCourseMaterials($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related CourseMaterials from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CourseMaterial[] List of CourseMaterial objects
     */
    public function getCourseMaterialsJoinCourseFolder($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseMaterialQuery::create(null, $criteria);
        $query->joinWith('CourseFolder', $join_behavior);

        return $this->getCourseMaterials($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related CourseMaterials from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CourseMaterial[] List of CourseMaterial objects
     */
    public function getCourseMaterialsJoinFile($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseMaterialQuery::create(null, $criteria);
        $query->joinWith('File', $join_behavior);

        return $this->getCourseMaterials($query, $con);
    }

    /**
     * Clears out the collNotifications collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addNotifications()
     */
    public function clearNotifications()
    {
        $this->collNotifications = null; // important to set this to null since that means it is uninitialized
        $this->collNotificationsPartial = null;

        return $this;
    }

    /**
     * reset is the collNotifications collection loaded partially
     *
     * @return void
     */
    public function resetPartialNotifications($v = true)
    {
        $this->collNotificationsPartial = $v;
    }

    /**
     * Initializes the collNotifications collection.
     *
     * By default this just sets the collNotifications collection to an empty array (like clearcollNotifications());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNotifications($overrideExisting = true)
    {
        if (null !== $this->collNotifications && !$overrideExisting) {
            return;
        }
        $this->collNotifications = new PropelObjectCollection();
        $this->collNotifications->setModel('Notification');
    }

    /**
     * Gets an array of Notification objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Notification[] List of Notification objects
     * @throws PropelException
     */
    public function getNotifications($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collNotificationsPartial && !$this->isNew();
        if (null === $this->collNotifications || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNotifications) {
                // return empty collection
                $this->initNotifications();
            } else {
                $collNotifications = NotificationQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collNotificationsPartial && count($collNotifications)) {
                      $this->initNotifications(false);

                      foreach($collNotifications as $obj) {
                        if (false == $this->collNotifications->contains($obj)) {
                          $this->collNotifications->append($obj);
                        }
                      }

                      $this->collNotificationsPartial = true;
                    }

                    $collNotifications->getInternalIterator()->rewind();
                    return $collNotifications;
                }

                if($partial && $this->collNotifications) {
                    foreach($this->collNotifications as $obj) {
                        if($obj->isNew()) {
                            $collNotifications[] = $obj;
                        }
                    }
                }

                $this->collNotifications = $collNotifications;
                $this->collNotificationsPartial = false;
            }
        }

        return $this->collNotifications;
    }

    /**
     * Sets a collection of Notification objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $notifications A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setNotifications(PropelCollection $notifications, PropelPDO $con = null)
    {
        $notificationsToDelete = $this->getNotifications(new Criteria(), $con)->diff($notifications);

        $this->notificationsScheduledForDeletion = unserialize(serialize($notificationsToDelete));

        foreach ($notificationsToDelete as $notificationRemoved) {
            $notificationRemoved->setCourse(null);
        }

        $this->collNotifications = null;
        foreach ($notifications as $notification) {
            $this->addNotification($notification);
        }

        $this->collNotifications = $notifications;
        $this->collNotificationsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Notification objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Notification objects.
     * @throws PropelException
     */
    public function countNotifications(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collNotificationsPartial && !$this->isNew();
        if (null === $this->collNotifications || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotifications) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getNotifications());
            }
            $query = NotificationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCourse($this)
                ->count($con);
        }

        return count($this->collNotifications);
    }

    /**
     * Method called to associate a Notification object to this object
     * through the Notification foreign key attribute.
     *
     * @param    Notification $l Notification
     * @return Course The current object (for fluent API support)
     */
    public function addNotification(Notification $l)
    {
        if ($this->collNotifications === null) {
            $this->initNotifications();
            $this->collNotificationsPartial = true;
        }
        if (!in_array($l, $this->collNotifications->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddNotification($l);
        }

        return $this;
    }

    /**
     * @param	Notification $notification The notification object to add.
     */
    protected function doAddNotification($notification)
    {
        $this->collNotifications[]= $notification;
        $notification->setCourse($this);
    }

    /**
     * @param	Notification $notification The notification object to remove.
     * @return Course The current object (for fluent API support)
     */
    public function removeNotification($notification)
    {
        if ($this->getNotifications()->contains($notification)) {
            $this->collNotifications->remove($this->collNotifications->search($notification));
            if (null === $this->notificationsScheduledForDeletion) {
                $this->notificationsScheduledForDeletion = clone $this->collNotifications;
                $this->notificationsScheduledForDeletion->clear();
            }
            $this->notificationsScheduledForDeletion[]= $notification;
            $notification->setCourse(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related Notifications from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Notification[] List of Notification objects
     */
    public function getNotificationsJoinAssignment($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = NotificationQuery::create(null, $criteria);
        $query->joinWith('Assignment', $join_behavior);

        return $this->getNotifications($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related Notifications from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Notification[] List of Notification objects
     */
    public function getNotificationsJoinUserRelatedByCreatedBy($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = NotificationQuery::create(null, $criteria);
        $query->joinWith('UserRelatedByCreatedBy', $join_behavior);

        return $this->getNotifications($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Course is new, it will return
     * an empty collection; or if this Course has previously
     * been saved, it will retrieve related Notifications from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Course.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Notification[] List of Notification objects
     */
    public function getNotificationsJoinUserRelatedByUserId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = NotificationQuery::create(null, $criteria);
        $query->joinWith('UserRelatedByUserId', $join_behavior);

        return $this->getNotifications($query, $con);
    }

    /**
     * Clears out the collStudents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addStudents()
     */
    public function clearStudents()
    {
        $this->collStudents = null; // important to set this to null since that means it is uninitialized
        $this->collStudentsPartial = null;

        return $this;
    }

    /**
     * Initializes the collStudents collection.
     *
     * By default this just sets the collStudents collection to an empty collection (like clearStudents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initStudents()
    {
        $this->collStudents = new PropelObjectCollection();
        $this->collStudents->setModel('Student');
    }

    /**
     * Gets a collection of Student objects related by a many-to-many relationship
     * to the current object by way of the course_students cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Student[] List of Student objects
     */
    public function getStudents($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collStudents || null !== $criteria) {
            if ($this->isNew() && null === $this->collStudents) {
                // return empty collection
                $this->initStudents();
            } else {
                $collStudents = StudentQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collStudents;
                }
                $this->collStudents = $collStudents;
            }
        }

        return $this->collStudents;
    }

    /**
     * Sets a collection of Student objects related by a many-to-many relationship
     * to the current object by way of the course_students cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $students A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setStudents(PropelCollection $students, PropelPDO $con = null)
    {
        $this->clearStudents();
        $currentStudents = $this->getStudents();

        $this->studentsScheduledForDeletion = $currentStudents->diff($students);

        foreach ($students as $student) {
            if (!$currentStudents->contains($student)) {
                $this->doAddStudent($student);
            }
        }

        $this->collStudents = $students;

        return $this;
    }

    /**
     * Gets the number of Student objects related by a many-to-many relationship
     * to the current object by way of the course_students cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Student objects
     */
    public function countStudents($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collStudents || null !== $criteria) {
            if ($this->isNew() && null === $this->collStudents) {
                return 0;
            } else {
                $query = StudentQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByCourse($this)
                    ->count($con);
            }
        } else {
            return count($this->collStudents);
        }
    }

    /**
     * Associate a Student object to this object
     * through the course_students cross reference table.
     *
     * @param  Student $student The CourseStudent object to relate
     * @return Course The current object (for fluent API support)
     */
    public function addStudent(Student $student)
    {
        if ($this->collStudents === null) {
            $this->initStudents();
        }
        if (!$this->collStudents->contains($student)) { // only add it if the **same** object is not already associated
            $this->doAddStudent($student);

            $this->collStudents[]= $student;
        }

        return $this;
    }

    /**
     * @param	Student $student The student object to add.
     */
    protected function doAddStudent($student)
    {
        $courseStudent = new CourseStudent();
        $courseStudent->setStudent($student);
        $this->addCourseStudent($courseStudent);
    }

    /**
     * Remove a Student object to this object
     * through the course_students cross reference table.
     *
     * @param Student $student The CourseStudent object to relate
     * @return Course The current object (for fluent API support)
     */
    public function removeStudent(Student $student)
    {
        if ($this->getStudents()->contains($student)) {
            $this->collStudents->remove($this->collStudents->search($student));
            if (null === $this->studentsScheduledForDeletion) {
                $this->studentsScheduledForDeletion = clone $this->collStudents;
                $this->studentsScheduledForDeletion->clear();
            }
            $this->studentsScheduledForDeletion[]= $student;
        }

        return $this;
    }

    /**
     * Clears out the collTeachers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Course The current object (for fluent API support)
     * @see        addTeachers()
     */
    public function clearTeachers()
    {
        $this->collTeachers = null; // important to set this to null since that means it is uninitialized
        $this->collTeachersPartial = null;

        return $this;
    }

    /**
     * Initializes the collTeachers collection.
     *
     * By default this just sets the collTeachers collection to an empty collection (like clearTeachers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initTeachers()
    {
        $this->collTeachers = new PropelObjectCollection();
        $this->collTeachers->setModel('Teacher');
    }

    /**
     * Gets a collection of Teacher objects related by a many-to-many relationship
     * to the current object by way of the course_teachers cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Course is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Teacher[] List of Teacher objects
     */
    public function getTeachers($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collTeachers || null !== $criteria) {
            if ($this->isNew() && null === $this->collTeachers) {
                // return empty collection
                $this->initTeachers();
            } else {
                $collTeachers = TeacherQuery::create(null, $criteria)
                    ->filterByCourse($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collTeachers;
                }
                $this->collTeachers = $collTeachers;
            }
        }

        return $this->collTeachers;
    }

    /**
     * Sets a collection of Teacher objects related by a many-to-many relationship
     * to the current object by way of the course_teachers cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $teachers A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Course The current object (for fluent API support)
     */
    public function setTeachers(PropelCollection $teachers, PropelPDO $con = null)
    {
        $this->clearTeachers();
        $currentTeachers = $this->getTeachers();

        $this->teachersScheduledForDeletion = $currentTeachers->diff($teachers);

        foreach ($teachers as $teacher) {
            if (!$currentTeachers->contains($teacher)) {
                $this->doAddTeacher($teacher);
            }
        }

        $this->collTeachers = $teachers;

        return $this;
    }

    /**
     * Gets the number of Teacher objects related by a many-to-many relationship
     * to the current object by way of the course_teachers cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Teacher objects
     */
    public function countTeachers($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collTeachers || null !== $criteria) {
            if ($this->isNew() && null === $this->collTeachers) {
                return 0;
            } else {
                $query = TeacherQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByCourse($this)
                    ->count($con);
            }
        } else {
            return count($this->collTeachers);
        }
    }

    /**
     * Associate a Teacher object to this object
     * through the course_teachers cross reference table.
     *
     * @param  Teacher $teacher The CourseTeacher object to relate
     * @return Course The current object (for fluent API support)
     */
    public function addTeacher(Teacher $teacher)
    {
        if ($this->collTeachers === null) {
            $this->initTeachers();
        }
        if (!$this->collTeachers->contains($teacher)) { // only add it if the **same** object is not already associated
            $this->doAddTeacher($teacher);

            $this->collTeachers[]= $teacher;
        }

        return $this;
    }

    /**
     * @param	Teacher $teacher The teacher object to add.
     */
    protected function doAddTeacher($teacher)
    {
        $courseTeacher = new CourseTeacher();
        $courseTeacher->setTeacher($teacher);
        $this->addCourseTeacher($courseTeacher);
    }

    /**
     * Remove a Teacher object to this object
     * through the course_teachers cross reference table.
     *
     * @param Teacher $teacher The CourseTeacher object to relate
     * @return Course The current object (for fluent API support)
     */
    public function removeTeacher(Teacher $teacher)
    {
        if ($this->getTeachers()->contains($teacher)) {
            $this->collTeachers->remove($this->collTeachers->search($teacher));
            if (null === $this->teachersScheduledForDeletion) {
                $this->teachersScheduledForDeletion = clone $this->collTeachers;
                $this->teachersScheduledForDeletion->clear();
            }
            $this->teachersScheduledForDeletion[]= $teacher;
        }

        return $this;
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
        $this->access_code = null;
        $this->start = null;
        $this->end = null;
        $this->created_by = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
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
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
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
            if ($this->collAttendances) {
                foreach ($this->collAttendances as $o) {
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
            if ($this->collCourseScheduleDays) {
                foreach ($this->collCourseScheduleDays as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collFeedItems) {
                foreach ($this->collFeedItems as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourseFolders) {
                foreach ($this->collCourseFolders as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourseMaterials) {
                foreach ($this->collCourseMaterials as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collNotifications) {
                foreach ($this->collNotifications as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collStudents) {
                foreach ($this->collStudents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTeachers) {
                foreach ($this->collTeachers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCreatedByTeacher instanceof Persistent) {
              $this->aCreatedByTeacher->clearAllReferences($deep);
            }
            if ($this->aDiscipline instanceof Persistent) {
              $this->aDiscipline->clearAllReferences($deep);
            }
            if ($this->aGradeLevel instanceof Persistent) {
              $this->aGradeLevel->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collAssignmentCategories instanceof PropelCollection) {
            $this->collAssignmentCategories->clearIterator();
        }
        $this->collAssignmentCategories = null;
        if ($this->collAssignments instanceof PropelCollection) {
            $this->collAssignments->clearIterator();
        }
        $this->collAssignments = null;
        if ($this->collAttendances instanceof PropelCollection) {
            $this->collAttendances->clearIterator();
        }
        $this->collAttendances = null;
        if ($this->collCourseStudents instanceof PropelCollection) {
            $this->collCourseStudents->clearIterator();
        }
        $this->collCourseStudents = null;
        if ($this->collCourseTeachers instanceof PropelCollection) {
            $this->collCourseTeachers->clearIterator();
        }
        $this->collCourseTeachers = null;
        if ($this->collCourseScheduleDays instanceof PropelCollection) {
            $this->collCourseScheduleDays->clearIterator();
        }
        $this->collCourseScheduleDays = null;
        if ($this->collFeedItems instanceof PropelCollection) {
            $this->collFeedItems->clearIterator();
        }
        $this->collFeedItems = null;
        if ($this->collCourseFolders instanceof PropelCollection) {
            $this->collCourseFolders->clearIterator();
        }
        $this->collCourseFolders = null;
        if ($this->collCourseMaterials instanceof PropelCollection) {
            $this->collCourseMaterials->clearIterator();
        }
        $this->collCourseMaterials = null;
        if ($this->collNotifications instanceof PropelCollection) {
            $this->collNotifications->clearIterator();
        }
        $this->collNotifications = null;
        if ($this->collStudents instanceof PropelCollection) {
            $this->collStudents->clearIterator();
        }
        $this->collStudents = null;
        if ($this->collTeachers instanceof PropelCollection) {
            $this->collTeachers->clearIterator();
        }
        $this->collTeachers = null;
        $this->aCreatedByTeacher = null;
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
