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
use Zerebral\BusinessBundle\Model\Assignment\Assignment;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;
use Zerebral\BusinessBundle\Model\Attendance\Attendance;
use Zerebral\BusinessBundle\Model\Attendance\AttendanceQuery;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CourseQuery;
use Zerebral\BusinessBundle\Model\Course\CourseTeacher;
use Zerebral\BusinessBundle\Model\Course\CourseTeacherQuery;
use Zerebral\BusinessBundle\Model\Course\Discipline;
use Zerebral\BusinessBundle\Model\Course\DisciplineQuery;
use Zerebral\BusinessBundle\Model\Material\CourseMaterial;
use Zerebral\BusinessBundle\Model\Material\CourseMaterialQuery;
use Zerebral\BusinessBundle\Model\User\Teacher;
use Zerebral\BusinessBundle\Model\User\TeacherPeer;
use Zerebral\BusinessBundle\Model\User\TeacherQuery;
use Zerebral\BusinessBundle\Model\User\User;
use Zerebral\BusinessBundle\Model\User\UserQuery;

abstract class BaseTeacher extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Zerebral\\BusinessBundle\\Model\\User\\TeacherPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        TeacherPeer
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
     * The value for the subjects field.
     * @var        string
     */
    protected $subjects;

    /**
     * The value for the grades field.
     * @var        string
     */
    protected $grades;

    /**
     * @var        User
     */
    protected $aUser;

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
     * @var        PropelObjectCollection|Course[] Collection to store aggregation of Course objects.
     */
    protected $collCreatedByTeachers;
    protected $collCreatedByTeachersPartial;

    /**
     * @var        PropelObjectCollection|Discipline[] Collection to store aggregation of Discipline objects.
     */
    protected $collDisciplines;
    protected $collDisciplinesPartial;

    /**
     * @var        PropelObjectCollection|CourseTeacher[] Collection to store aggregation of CourseTeacher objects.
     */
    protected $collCourseTeachers;
    protected $collCourseTeachersPartial;

    /**
     * @var        PropelObjectCollection|CourseMaterial[] Collection to store aggregation of CourseMaterial objects.
     */
    protected $collCourseMaterials;
    protected $collCourseMaterialsPartial;

    /**
     * @var        PropelObjectCollection|Course[] Collection to store aggregation of Course objects.
     */
    protected $collCourses;

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
    protected $coursesScheduledForDeletion = null;

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
    protected $createdByTeachersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $disciplinesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseTeachersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseMaterialsScheduledForDeletion = null;

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
     * Get the [subjects] column value.
     *
     * @return string
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Get the [grades] column value.
     *
     * @return string
     */
    public function getGrades()
    {
        return $this->grades;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return Teacher The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = TeacherPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [user_id] column.
     *
     * @param int $v new value
     * @return Teacher The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = TeacherPeer::USER_ID;
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
     * @return Teacher The current object (for fluent API support)
     */
    public function setBio($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->bio !== $v) {
            $this->bio = $v;
            $this->modifiedColumns[] = TeacherPeer::BIO;
        }


        return $this;
    } // setBio()

    /**
     * Set the value of [subjects] column.
     *
     * @param string $v new value
     * @return Teacher The current object (for fluent API support)
     */
    public function setSubjects($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->subjects !== $v) {
            $this->subjects = $v;
            $this->modifiedColumns[] = TeacherPeer::SUBJECTS;
        }


        return $this;
    } // setSubjects()

    /**
     * Set the value of [grades] column.
     *
     * @param string $v new value
     * @return Teacher The current object (for fluent API support)
     */
    public function setGrades($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->grades !== $v) {
            $this->grades = $v;
            $this->modifiedColumns[] = TeacherPeer::GRADES;
        }


        return $this;
    } // setGrades()

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
            $this->subjects = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->grades = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 5; // 5 = TeacherPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Teacher object", $e);
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
            $con = Propel::getConnection(TeacherPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = TeacherPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUser = null;
            $this->collAssignmentCategories = null;

            $this->collAssignments = null;

            $this->collAttendances = null;

            $this->collCreatedByTeachers = null;

            $this->collDisciplines = null;

            $this->collCourseTeachers = null;

            $this->collCourseMaterials = null;

            $this->collCourses = null;
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
            $con = Propel::getConnection(TeacherPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = TeacherQuery::create()
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
            $con = Propel::getConnection(TeacherPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                TeacherPeer::addInstanceToPool($this);
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

            if ($this->coursesScheduledForDeletion !== null) {
                if (!$this->coursesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->coursesScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    CourseTeacherQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->coursesScheduledForDeletion = null;
                }

                foreach ($this->getCourses() as $course) {
                    if ($course->isModified()) {
                        $course->save($con);
                    }
                }
            } elseif ($this->collCourses) {
                foreach ($this->collCourses as $course) {
                    if ($course->isModified()) {
                        $course->save($con);
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

            if ($this->createdByTeachersScheduledForDeletion !== null) {
                if (!$this->createdByTeachersScheduledForDeletion->isEmpty()) {
                    CourseQuery::create()
                        ->filterByPrimaryKeys($this->createdByTeachersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->createdByTeachersScheduledForDeletion = null;
                }
            }

            if ($this->collCreatedByTeachers !== null) {
                foreach ($this->collCreatedByTeachers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->disciplinesScheduledForDeletion !== null) {
                if (!$this->disciplinesScheduledForDeletion->isEmpty()) {
                    foreach ($this->disciplinesScheduledForDeletion as $discipline) {
                        // need to save related object because we set the relation to null
                        $discipline->save($con);
                    }
                    $this->disciplinesScheduledForDeletion = null;
                }
            }

            if ($this->collDisciplines !== null) {
                foreach ($this->collDisciplines as $referrerFK) {
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

        $this->modifiedColumns[] = TeacherPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TeacherPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TeacherPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(TeacherPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(TeacherPeer::BIO)) {
            $modifiedColumns[':p' . $index++]  = '`bio`';
        }
        if ($this->isColumnModified(TeacherPeer::SUBJECTS)) {
            $modifiedColumns[':p' . $index++]  = '`subjects`';
        }
        if ($this->isColumnModified(TeacherPeer::GRADES)) {
            $modifiedColumns[':p' . $index++]  = '`grades`';
        }

        $sql = sprintf(
            'INSERT INTO `teachers` (%s) VALUES (%s)',
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
                    case '`subjects`':
                        $stmt->bindValue($identifier, $this->subjects, PDO::PARAM_STR);
                        break;
                    case '`grades`':
                        $stmt->bindValue($identifier, $this->grades, PDO::PARAM_STR);
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


            if (($retval = TeacherPeer::doValidate($this, $columns)) !== true) {
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

                if ($this->collCreatedByTeachers !== null) {
                    foreach ($this->collCreatedByTeachers as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collDisciplines !== null) {
                    foreach ($this->collDisciplines as $referrerFK) {
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

                if ($this->collCourseMaterials !== null) {
                    foreach ($this->collCourseMaterials as $referrerFK) {
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
        $pos = TeacherPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getSubjects();
                break;
            case 4:
                return $this->getGrades();
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
        if (isset($alreadyDumpedObjects['Teacher'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Teacher'][$this->getPrimaryKey()] = true;
        $keys = TeacherPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getBio(),
            $keys[3] => $this->getSubjects(),
            $keys[4] => $this->getGrades(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aUser) {
                $result['User'] = $this->aUser->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
            if (null !== $this->collCreatedByTeachers) {
                $result['CreatedByTeachers'] = $this->collCreatedByTeachers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDisciplines) {
                $result['Disciplines'] = $this->collDisciplines->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseTeachers) {
                $result['CourseTeachers'] = $this->collCourseTeachers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseMaterials) {
                $result['CourseMaterials'] = $this->collCourseMaterials->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = TeacherPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setSubjects($value);
                break;
            case 4:
                $this->setGrades($value);
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
        $keys = TeacherPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setBio($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSubjects($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setGrades($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TeacherPeer::DATABASE_NAME);

        if ($this->isColumnModified(TeacherPeer::ID)) $criteria->add(TeacherPeer::ID, $this->id);
        if ($this->isColumnModified(TeacherPeer::USER_ID)) $criteria->add(TeacherPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(TeacherPeer::BIO)) $criteria->add(TeacherPeer::BIO, $this->bio);
        if ($this->isColumnModified(TeacherPeer::SUBJECTS)) $criteria->add(TeacherPeer::SUBJECTS, $this->subjects);
        if ($this->isColumnModified(TeacherPeer::GRADES)) $criteria->add(TeacherPeer::GRADES, $this->grades);

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
        $criteria = new Criteria(TeacherPeer::DATABASE_NAME);
        $criteria->add(TeacherPeer::ID, $this->id);

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
     * @param object $copyObj An object of Teacher (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setBio($this->getBio());
        $copyObj->setSubjects($this->getSubjects());
        $copyObj->setGrades($this->getGrades());

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

            foreach ($this->getCreatedByTeachers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCreatedByTeacher($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDisciplines() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDiscipline($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCourseTeachers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseTeacher($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCourseMaterials() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseMaterial($relObj->copy($deepCopy));
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
     * @return Teacher Clone of current object.
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
     * @return TeacherPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new TeacherPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param             User $v
     * @return Teacher The current object (for fluent API support)
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
            $v->addTeacher($this);
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
                $this->aUser->addTeachers($this);
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
        if ('AssignmentCategory' == $relationName) {
            $this->initAssignmentCategories();
        }
        if ('Assignment' == $relationName) {
            $this->initAssignments();
        }
        if ('Attendance' == $relationName) {
            $this->initAttendances();
        }
        if ('CreatedByTeacher' == $relationName) {
            $this->initCreatedByTeachers();
        }
        if ('Discipline' == $relationName) {
            $this->initDisciplines();
        }
        if ('CourseTeacher' == $relationName) {
            $this->initCourseTeachers();
        }
        if ('CourseMaterial' == $relationName) {
            $this->initCourseMaterials();
        }
    }

    /**
     * Clears out the collAssignmentCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Teacher The current object (for fluent API support)
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
     * If this Teacher is new, it will return
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
                    ->filterByTeacher($this)
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
     * @return Teacher The current object (for fluent API support)
     */
    public function setAssignmentCategories(PropelCollection $assignmentCategories, PropelPDO $con = null)
    {
        $assignmentCategoriesToDelete = $this->getAssignmentCategories(new Criteria(), $con)->diff($assignmentCategories);

        $this->assignmentCategoriesScheduledForDeletion = unserialize(serialize($assignmentCategoriesToDelete));

        foreach ($assignmentCategoriesToDelete as $assignmentCategoryRemoved) {
            $assignmentCategoryRemoved->setTeacher(null);
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
                ->filterByTeacher($this)
                ->count($con);
        }

        return count($this->collAssignmentCategories);
    }

    /**
     * Method called to associate a AssignmentCategory object to this object
     * through the AssignmentCategory foreign key attribute.
     *
     * @param    AssignmentCategory $l AssignmentCategory
     * @return Teacher The current object (for fluent API support)
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
        $assignmentCategory->setTeacher($this);
    }

    /**
     * @param	AssignmentCategory $assignmentCategory The assignmentCategory object to remove.
     * @return Teacher The current object (for fluent API support)
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
            $assignmentCategory->setTeacher(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related AssignmentCategories from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|AssignmentCategory[] List of AssignmentCategory objects
     */
    public function getAssignmentCategoriesJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AssignmentCategoryQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getAssignmentCategories($query, $con);
    }

    /**
     * Clears out the collAssignments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Teacher The current object (for fluent API support)
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
     * If this Teacher is new, it will return
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
                    ->filterByTeacher($this)
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
     * @return Teacher The current object (for fluent API support)
     */
    public function setAssignments(PropelCollection $assignments, PropelPDO $con = null)
    {
        $assignmentsToDelete = $this->getAssignments(new Criteria(), $con)->diff($assignments);

        $this->assignmentsScheduledForDeletion = unserialize(serialize($assignmentsToDelete));

        foreach ($assignmentsToDelete as $assignmentRemoved) {
            $assignmentRemoved->setTeacher(null);
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
                ->filterByTeacher($this)
                ->count($con);
        }

        return count($this->collAssignments);
    }

    /**
     * Method called to associate a Assignment object to this object
     * through the Assignment foreign key attribute.
     *
     * @param    Assignment $l Assignment
     * @return Teacher The current object (for fluent API support)
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
        $assignment->setTeacher($this);
    }

    /**
     * @param	Assignment $assignment The assignment object to remove.
     * @return Teacher The current object (for fluent API support)
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
            $assignment->setTeacher(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related Assignments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Assignment[] List of Assignment objects
     */
    public function getAssignmentsJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AssignmentQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getAssignments($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related Assignments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
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
     * @return Teacher The current object (for fluent API support)
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
     * If this Teacher is new, it will return
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
                    ->filterByTeacher($this)
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
     * @return Teacher The current object (for fluent API support)
     */
    public function setAttendances(PropelCollection $attendances, PropelPDO $con = null)
    {
        $attendancesToDelete = $this->getAttendances(new Criteria(), $con)->diff($attendances);

        $this->attendancesScheduledForDeletion = unserialize(serialize($attendancesToDelete));

        foreach ($attendancesToDelete as $attendanceRemoved) {
            $attendanceRemoved->setTeacher(null);
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
                ->filterByTeacher($this)
                ->count($con);
        }

        return count($this->collAttendances);
    }

    /**
     * Method called to associate a Attendance object to this object
     * through the Attendance foreign key attribute.
     *
     * @param    Attendance $l Attendance
     * @return Teacher The current object (for fluent API support)
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
        $attendance->setTeacher($this);
    }

    /**
     * @param	Attendance $attendance The attendance object to remove.
     * @return Teacher The current object (for fluent API support)
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
            $attendance->setTeacher(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related Attendances from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Attendance[] List of Attendance objects
     */
    public function getAttendancesJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AttendanceQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getAttendances($query, $con);
    }

    /**
     * Clears out the collCreatedByTeachers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Teacher The current object (for fluent API support)
     * @see        addCreatedByTeachers()
     */
    public function clearCreatedByTeachers()
    {
        $this->collCreatedByTeachers = null; // important to set this to null since that means it is uninitialized
        $this->collCreatedByTeachersPartial = null;

        return $this;
    }

    /**
     * reset is the collCreatedByTeachers collection loaded partially
     *
     * @return void
     */
    public function resetPartialCreatedByTeachers($v = true)
    {
        $this->collCreatedByTeachersPartial = $v;
    }

    /**
     * Initializes the collCreatedByTeachers collection.
     *
     * By default this just sets the collCreatedByTeachers collection to an empty array (like clearcollCreatedByTeachers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCreatedByTeachers($overrideExisting = true)
    {
        if (null !== $this->collCreatedByTeachers && !$overrideExisting) {
            return;
        }
        $this->collCreatedByTeachers = new PropelObjectCollection();
        $this->collCreatedByTeachers->setModel('Course');
    }

    /**
     * Gets an array of Course objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Teacher is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Course[] List of Course objects
     * @throws PropelException
     */
    public function getCreatedByTeachers($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCreatedByTeachersPartial && !$this->isNew();
        if (null === $this->collCreatedByTeachers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCreatedByTeachers) {
                // return empty collection
                $this->initCreatedByTeachers();
            } else {
                $collCreatedByTeachers = CourseQuery::create(null, $criteria)
                    ->filterByCreatedByTeacher($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCreatedByTeachersPartial && count($collCreatedByTeachers)) {
                      $this->initCreatedByTeachers(false);

                      foreach($collCreatedByTeachers as $obj) {
                        if (false == $this->collCreatedByTeachers->contains($obj)) {
                          $this->collCreatedByTeachers->append($obj);
                        }
                      }

                      $this->collCreatedByTeachersPartial = true;
                    }

                    $collCreatedByTeachers->getInternalIterator()->rewind();
                    return $collCreatedByTeachers;
                }

                if($partial && $this->collCreatedByTeachers) {
                    foreach($this->collCreatedByTeachers as $obj) {
                        if($obj->isNew()) {
                            $collCreatedByTeachers[] = $obj;
                        }
                    }
                }

                $this->collCreatedByTeachers = $collCreatedByTeachers;
                $this->collCreatedByTeachersPartial = false;
            }
        }

        return $this->collCreatedByTeachers;
    }

    /**
     * Sets a collection of CreatedByTeacher objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $createdByTeachers A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Teacher The current object (for fluent API support)
     */
    public function setCreatedByTeachers(PropelCollection $createdByTeachers, PropelPDO $con = null)
    {
        $createdByTeachersToDelete = $this->getCreatedByTeachers(new Criteria(), $con)->diff($createdByTeachers);

        $this->createdByTeachersScheduledForDeletion = unserialize(serialize($createdByTeachersToDelete));

        foreach ($createdByTeachersToDelete as $createdByTeacherRemoved) {
            $createdByTeacherRemoved->setCreatedByTeacher(null);
        }

        $this->collCreatedByTeachers = null;
        foreach ($createdByTeachers as $createdByTeacher) {
            $this->addCreatedByTeacher($createdByTeacher);
        }

        $this->collCreatedByTeachers = $createdByTeachers;
        $this->collCreatedByTeachersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Course objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Course objects.
     * @throws PropelException
     */
    public function countCreatedByTeachers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCreatedByTeachersPartial && !$this->isNew();
        if (null === $this->collCreatedByTeachers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCreatedByTeachers) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getCreatedByTeachers());
            }
            $query = CourseQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCreatedByTeacher($this)
                ->count($con);
        }

        return count($this->collCreatedByTeachers);
    }

    /**
     * Method called to associate a Course object to this object
     * through the Course foreign key attribute.
     *
     * @param    Course $l Course
     * @return Teacher The current object (for fluent API support)
     */
    public function addCreatedByTeacher(Course $l)
    {
        if ($this->collCreatedByTeachers === null) {
            $this->initCreatedByTeachers();
            $this->collCreatedByTeachersPartial = true;
        }
        if (!in_array($l, $this->collCreatedByTeachers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCreatedByTeacher($l);
        }

        return $this;
    }

    /**
     * @param	CreatedByTeacher $createdByTeacher The createdByTeacher object to add.
     */
    protected function doAddCreatedByTeacher($createdByTeacher)
    {
        $this->collCreatedByTeachers[]= $createdByTeacher;
        $createdByTeacher->setCreatedByTeacher($this);
    }

    /**
     * @param	CreatedByTeacher $createdByTeacher The createdByTeacher object to remove.
     * @return Teacher The current object (for fluent API support)
     */
    public function removeCreatedByTeacher($createdByTeacher)
    {
        if ($this->getCreatedByTeachers()->contains($createdByTeacher)) {
            $this->collCreatedByTeachers->remove($this->collCreatedByTeachers->search($createdByTeacher));
            if (null === $this->createdByTeachersScheduledForDeletion) {
                $this->createdByTeachersScheduledForDeletion = clone $this->collCreatedByTeachers;
                $this->createdByTeachersScheduledForDeletion->clear();
            }
            $this->createdByTeachersScheduledForDeletion[]= clone $createdByTeacher;
            $createdByTeacher->setCreatedByTeacher(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related CreatedByTeachers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Course[] List of Course objects
     */
    public function getCreatedByTeachersJoinDiscipline($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseQuery::create(null, $criteria);
        $query->joinWith('Discipline', $join_behavior);

        return $this->getCreatedByTeachers($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related CreatedByTeachers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Course[] List of Course objects
     */
    public function getCreatedByTeachersJoinGradeLevel($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseQuery::create(null, $criteria);
        $query->joinWith('GradeLevel', $join_behavior);

        return $this->getCreatedByTeachers($query, $con);
    }

    /**
     * Clears out the collDisciplines collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Teacher The current object (for fluent API support)
     * @see        addDisciplines()
     */
    public function clearDisciplines()
    {
        $this->collDisciplines = null; // important to set this to null since that means it is uninitialized
        $this->collDisciplinesPartial = null;

        return $this;
    }

    /**
     * reset is the collDisciplines collection loaded partially
     *
     * @return void
     */
    public function resetPartialDisciplines($v = true)
    {
        $this->collDisciplinesPartial = $v;
    }

    /**
     * Initializes the collDisciplines collection.
     *
     * By default this just sets the collDisciplines collection to an empty array (like clearcollDisciplines());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDisciplines($overrideExisting = true)
    {
        if (null !== $this->collDisciplines && !$overrideExisting) {
            return;
        }
        $this->collDisciplines = new PropelObjectCollection();
        $this->collDisciplines->setModel('Discipline');
    }

    /**
     * Gets an array of Discipline objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Teacher is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Discipline[] List of Discipline objects
     * @throws PropelException
     */
    public function getDisciplines($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collDisciplinesPartial && !$this->isNew();
        if (null === $this->collDisciplines || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDisciplines) {
                // return empty collection
                $this->initDisciplines();
            } else {
                $collDisciplines = DisciplineQuery::create(null, $criteria)
                    ->filterByTeacher($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collDisciplinesPartial && count($collDisciplines)) {
                      $this->initDisciplines(false);

                      foreach($collDisciplines as $obj) {
                        if (false == $this->collDisciplines->contains($obj)) {
                          $this->collDisciplines->append($obj);
                        }
                      }

                      $this->collDisciplinesPartial = true;
                    }

                    $collDisciplines->getInternalIterator()->rewind();
                    return $collDisciplines;
                }

                if($partial && $this->collDisciplines) {
                    foreach($this->collDisciplines as $obj) {
                        if($obj->isNew()) {
                            $collDisciplines[] = $obj;
                        }
                    }
                }

                $this->collDisciplines = $collDisciplines;
                $this->collDisciplinesPartial = false;
            }
        }

        return $this->collDisciplines;
    }

    /**
     * Sets a collection of Discipline objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $disciplines A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Teacher The current object (for fluent API support)
     */
    public function setDisciplines(PropelCollection $disciplines, PropelPDO $con = null)
    {
        $disciplinesToDelete = $this->getDisciplines(new Criteria(), $con)->diff($disciplines);

        $this->disciplinesScheduledForDeletion = unserialize(serialize($disciplinesToDelete));

        foreach ($disciplinesToDelete as $disciplineRemoved) {
            $disciplineRemoved->setTeacher(null);
        }

        $this->collDisciplines = null;
        foreach ($disciplines as $discipline) {
            $this->addDiscipline($discipline);
        }

        $this->collDisciplines = $disciplines;
        $this->collDisciplinesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Discipline objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Discipline objects.
     * @throws PropelException
     */
    public function countDisciplines(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collDisciplinesPartial && !$this->isNew();
        if (null === $this->collDisciplines || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDisciplines) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getDisciplines());
            }
            $query = DisciplineQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByTeacher($this)
                ->count($con);
        }

        return count($this->collDisciplines);
    }

    /**
     * Method called to associate a Discipline object to this object
     * through the Discipline foreign key attribute.
     *
     * @param    Discipline $l Discipline
     * @return Teacher The current object (for fluent API support)
     */
    public function addDiscipline(Discipline $l)
    {
        if ($this->collDisciplines === null) {
            $this->initDisciplines();
            $this->collDisciplinesPartial = true;
        }
        if (!in_array($l, $this->collDisciplines->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDiscipline($l);
        }

        return $this;
    }

    /**
     * @param	Discipline $discipline The discipline object to add.
     */
    protected function doAddDiscipline($discipline)
    {
        $this->collDisciplines[]= $discipline;
        $discipline->setTeacher($this);
    }

    /**
     * @param	Discipline $discipline The discipline object to remove.
     * @return Teacher The current object (for fluent API support)
     */
    public function removeDiscipline($discipline)
    {
        if ($this->getDisciplines()->contains($discipline)) {
            $this->collDisciplines->remove($this->collDisciplines->search($discipline));
            if (null === $this->disciplinesScheduledForDeletion) {
                $this->disciplinesScheduledForDeletion = clone $this->collDisciplines;
                $this->disciplinesScheduledForDeletion->clear();
            }
            $this->disciplinesScheduledForDeletion[]= $discipline;
            $discipline->setTeacher(null);
        }

        return $this;
    }

    /**
     * Clears out the collCourseTeachers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Teacher The current object (for fluent API support)
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
     * If this Teacher is new, it will return
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
                    ->filterByTeacher($this)
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
     * @return Teacher The current object (for fluent API support)
     */
    public function setCourseTeachers(PropelCollection $courseTeachers, PropelPDO $con = null)
    {
        $courseTeachersToDelete = $this->getCourseTeachers(new Criteria(), $con)->diff($courseTeachers);

        $this->courseTeachersScheduledForDeletion = unserialize(serialize($courseTeachersToDelete));

        foreach ($courseTeachersToDelete as $courseTeacherRemoved) {
            $courseTeacherRemoved->setTeacher(null);
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
                ->filterByTeacher($this)
                ->count($con);
        }

        return count($this->collCourseTeachers);
    }

    /**
     * Method called to associate a CourseTeacher object to this object
     * through the CourseTeacher foreign key attribute.
     *
     * @param    CourseTeacher $l CourseTeacher
     * @return Teacher The current object (for fluent API support)
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
        $courseTeacher->setTeacher($this);
    }

    /**
     * @param	CourseTeacher $courseTeacher The courseTeacher object to remove.
     * @return Teacher The current object (for fluent API support)
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
            $courseTeacher->setTeacher(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related CourseTeachers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CourseTeacher[] List of CourseTeacher objects
     */
    public function getCourseTeachersJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseTeacherQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getCourseTeachers($query, $con);
    }

    /**
     * Clears out the collCourseMaterials collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Teacher The current object (for fluent API support)
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
     * If this Teacher is new, it will return
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
                    ->filterByTeacher($this)
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
     * @return Teacher The current object (for fluent API support)
     */
    public function setCourseMaterials(PropelCollection $courseMaterials, PropelPDO $con = null)
    {
        $courseMaterialsToDelete = $this->getCourseMaterials(new Criteria(), $con)->diff($courseMaterials);

        $this->courseMaterialsScheduledForDeletion = unserialize(serialize($courseMaterialsToDelete));

        foreach ($courseMaterialsToDelete as $courseMaterialRemoved) {
            $courseMaterialRemoved->setTeacher(null);
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
                ->filterByTeacher($this)
                ->count($con);
        }

        return count($this->collCourseMaterials);
    }

    /**
     * Method called to associate a CourseMaterial object to this object
     * through the CourseMaterial foreign key attribute.
     *
     * @param    CourseMaterial $l CourseMaterial
     * @return Teacher The current object (for fluent API support)
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
        $courseMaterial->setTeacher($this);
    }

    /**
     * @param	CourseMaterial $courseMaterial The courseMaterial object to remove.
     * @return Teacher The current object (for fluent API support)
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
            $courseMaterial->setTeacher(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related CourseMaterials from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CourseMaterial[] List of CourseMaterial objects
     */
    public function getCourseMaterialsJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CourseMaterialQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getCourseMaterials($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related CourseMaterials from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
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
     * Otherwise if this Teacher is new, it will return
     * an empty collection; or if this Teacher has previously
     * been saved, it will retrieve related CourseMaterials from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Teacher.
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
     * Clears out the collCourses collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Teacher The current object (for fluent API support)
     * @see        addCourses()
     */
    public function clearCourses()
    {
        $this->collCourses = null; // important to set this to null since that means it is uninitialized
        $this->collCoursesPartial = null;

        return $this;
    }

    /**
     * Initializes the collCourses collection.
     *
     * By default this just sets the collCourses collection to an empty collection (like clearCourses());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initCourses()
    {
        $this->collCourses = new PropelObjectCollection();
        $this->collCourses->setModel('Course');
    }

    /**
     * Gets a collection of Course objects related by a many-to-many relationship
     * to the current object by way of the course_teachers cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Teacher is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Course[] List of Course objects
     */
    public function getCourses($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collCourses || null !== $criteria) {
            if ($this->isNew() && null === $this->collCourses) {
                // return empty collection
                $this->initCourses();
            } else {
                $collCourses = CourseQuery::create(null, $criteria)
                    ->filterByTeacher($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collCourses;
                }
                $this->collCourses = $collCourses;
            }
        }

        return $this->collCourses;
    }

    /**
     * Sets a collection of Course objects related by a many-to-many relationship
     * to the current object by way of the course_teachers cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $courses A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Teacher The current object (for fluent API support)
     */
    public function setCourses(PropelCollection $courses, PropelPDO $con = null)
    {
        $this->clearCourses();
        $currentCourses = $this->getCourses();

        $this->coursesScheduledForDeletion = $currentCourses->diff($courses);

        foreach ($courses as $course) {
            if (!$currentCourses->contains($course)) {
                $this->doAddCourse($course);
            }
        }

        $this->collCourses = $courses;

        return $this;
    }

    /**
     * Gets the number of Course objects related by a many-to-many relationship
     * to the current object by way of the course_teachers cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Course objects
     */
    public function countCourses($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collCourses || null !== $criteria) {
            if ($this->isNew() && null === $this->collCourses) {
                return 0;
            } else {
                $query = CourseQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByTeacher($this)
                    ->count($con);
            }
        } else {
            return count($this->collCourses);
        }
    }

    /**
     * Associate a Course object to this object
     * through the course_teachers cross reference table.
     *
     * @param  Course $course The CourseTeacher object to relate
     * @return Teacher The current object (for fluent API support)
     */
    public function addCourse(Course $course)
    {
        if ($this->collCourses === null) {
            $this->initCourses();
        }
        if (!$this->collCourses->contains($course)) { // only add it if the **same** object is not already associated
            $this->doAddCourse($course);

            $this->collCourses[]= $course;
        }

        return $this;
    }

    /**
     * @param	Course $course The course object to add.
     */
    protected function doAddCourse($course)
    {
        $courseTeacher = new CourseTeacher();
        $courseTeacher->setCourse($course);
        $this->addCourseTeacher($courseTeacher);
    }

    /**
     * Remove a Course object to this object
     * through the course_teachers cross reference table.
     *
     * @param Course $course The CourseTeacher object to relate
     * @return Teacher The current object (for fluent API support)
     */
    public function removeCourse(Course $course)
    {
        if ($this->getCourses()->contains($course)) {
            $this->collCourses->remove($this->collCourses->search($course));
            if (null === $this->coursesScheduledForDeletion) {
                $this->coursesScheduledForDeletion = clone $this->collCourses;
                $this->coursesScheduledForDeletion->clear();
            }
            $this->coursesScheduledForDeletion[]= $course;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->user_id = null;
        $this->bio = null;
        $this->subjects = null;
        $this->grades = null;
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
            if ($this->collCreatedByTeachers) {
                foreach ($this->collCreatedByTeachers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDisciplines) {
                foreach ($this->collDisciplines as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourseTeachers) {
                foreach ($this->collCourseTeachers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourseMaterials) {
                foreach ($this->collCourseMaterials as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourses) {
                foreach ($this->collCourses as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aUser instanceof Persistent) {
              $this->aUser->clearAllReferences($deep);
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
        if ($this->collCreatedByTeachers instanceof PropelCollection) {
            $this->collCreatedByTeachers->clearIterator();
        }
        $this->collCreatedByTeachers = null;
        if ($this->collDisciplines instanceof PropelCollection) {
            $this->collDisciplines->clearIterator();
        }
        $this->collDisciplines = null;
        if ($this->collCourseTeachers instanceof PropelCollection) {
            $this->collCourseTeachers->clearIterator();
        }
        $this->collCourseTeachers = null;
        if ($this->collCourseMaterials instanceof PropelCollection) {
            $this->collCourseMaterials->clearIterator();
        }
        $this->collCourseMaterials = null;
        if ($this->collCourses instanceof PropelCollection) {
            $this->collCourses->clearIterator();
        }
        $this->collCourses = null;
        $this->aUser = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(TeacherPeer::DEFAULT_STRING_FORMAT);
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
