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
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignment;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;
use Zerebral\BusinessBundle\Model\Attendance\StudentAttendance;
use Zerebral\BusinessBundle\Model\Attendance\StudentAttendanceQuery;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CourseQuery;
use Zerebral\BusinessBundle\Model\Course\CourseStudent;
use Zerebral\BusinessBundle\Model\Course\CourseStudentQuery;
use Zerebral\BusinessBundle\Model\User\Guardian;
use Zerebral\BusinessBundle\Model\User\GuardianInvite;
use Zerebral\BusinessBundle\Model\User\GuardianInviteQuery;
use Zerebral\BusinessBundle\Model\User\GuardianQuery;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\StudentGuardian;
use Zerebral\BusinessBundle\Model\User\StudentGuardianQuery;
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
     * @var        PropelObjectCollection|StudentAttendance[] Collection to store aggregation of StudentAttendance objects.
     */
    protected $collStudentAttendances;
    protected $collStudentAttendancesPartial;

    /**
     * @var        PropelObjectCollection|CourseStudent[] Collection to store aggregation of CourseStudent objects.
     */
    protected $collCourseStudents;
    protected $collCourseStudentsPartial;

    /**
     * @var        PropelObjectCollection|StudentGuardian[] Collection to store aggregation of StudentGuardian objects.
     */
    protected $collStudentGuardians;
    protected $collStudentGuardiansPartial;

    /**
     * @var        PropelObjectCollection|GuardianInvite[] Collection to store aggregation of GuardianInvite objects.
     */
    protected $collGuardianInvites;
    protected $collGuardianInvitesPartial;

    /**
     * @var        PropelObjectCollection|Assignment[] Collection to store aggregation of Assignment objects.
     */
    protected $collAssignments;

    /**
     * @var        PropelObjectCollection|Course[] Collection to store aggregation of Course objects.
     */
    protected $collCourses;

    /**
     * @var        PropelObjectCollection|Guardian[] Collection to store aggregation of Guardian objects.
     */
    protected $collGuardians;

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
    protected $assignmentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $coursesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $guardiansScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $studentAssignmentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $studentAttendancesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $courseStudentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $studentGuardiansScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $guardianInvitesScheduledForDeletion = null;

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
        if ($v !== null && is_numeric($v)) {
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
        if ($v !== null && is_numeric($v)) {
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
        if ($v !== null && is_numeric($v)) {
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
        if ($v !== null && is_numeric($v)) {
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
        if ($v !== null && is_numeric($v)) {
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

            $this->collStudentAttendances = null;

            $this->collCourseStudents = null;

            $this->collStudentGuardians = null;

            $this->collGuardianInvites = null;

            $this->collAssignments = null;
            $this->collCourses = null;
            $this->collGuardians = null;
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
            $isInsert = $this->isNew();
            $isUpdate = $this->isModified();

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

            if ($this->assignmentsScheduledForDeletion !== null) {
                if (!$this->assignmentsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->assignmentsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    StudentAssignmentQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->assignmentsScheduledForDeletion = null;
                }

                foreach ($this->getAssignments() as $assignment) {
                    if ($assignment->isModified()) {
                        $assignment->save($con);
                    }
                }
            } elseif ($this->collAssignments) {
                foreach ($this->collAssignments as $assignment) {
                    if ($assignment->isModified()) {
                        $assignment->save($con);
                    }
                }
            }

            if ($this->coursesScheduledForDeletion !== null) {
                if (!$this->coursesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->coursesScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($remotePk, $pk);
                    }
                    CourseStudentQuery::create()
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

            if ($this->guardiansScheduledForDeletion !== null) {
                if (!$this->guardiansScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->guardiansScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    StudentGuardianQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->guardiansScheduledForDeletion = null;
                }

                foreach ($this->getGuardians() as $guardian) {
                    if ($guardian->isModified()) {
                        $guardian->save($con);
                    }
                }
            } elseif ($this->collGuardians) {
                foreach ($this->collGuardians as $guardian) {
                    if ($guardian->isModified()) {
                        $guardian->save($con);
                    }
                }
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

            if ($this->studentAttendancesScheduledForDeletion !== null) {
                if (!$this->studentAttendancesScheduledForDeletion->isEmpty()) {
                    StudentAttendanceQuery::create()
                        ->filterByPrimaryKeys($this->studentAttendancesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->studentAttendancesScheduledForDeletion = null;
                }
            }

            if ($this->collStudentAttendances !== null) {
                foreach ($this->collStudentAttendances as $referrerFK) {
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

            if ($this->studentGuardiansScheduledForDeletion !== null) {
                if (!$this->studentGuardiansScheduledForDeletion->isEmpty()) {
                    StudentGuardianQuery::create()
                        ->filterByPrimaryKeys($this->studentGuardiansScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->studentGuardiansScheduledForDeletion = null;
                }
            }

            if ($this->collStudentGuardians !== null) {
                foreach ($this->collStudentGuardians as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->guardianInvitesScheduledForDeletion !== null) {
                if (!$this->guardianInvitesScheduledForDeletion->isEmpty()) {
                    GuardianInviteQuery::create()
                        ->filterByPrimaryKeys($this->guardianInvitesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->guardianInvitesScheduledForDeletion = null;
                }
            }

            if ($this->collGuardianInvites !== null) {
                foreach ($this->collGuardianInvites as $referrerFK) {
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

                if ($this->collStudentAttendances !== null) {
                    foreach ($this->collStudentAttendances as $referrerFK) {
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

                if ($this->collStudentGuardians !== null) {
                    foreach ($this->collStudentGuardians as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collGuardianInvites !== null) {
                    foreach ($this->collGuardianInvites as $referrerFK) {
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
            if (null !== $this->collStudentAttendances) {
                $result['StudentAttendances'] = $this->collStudentAttendances->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCourseStudents) {
                $result['CourseStudents'] = $this->collCourseStudents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collStudentGuardians) {
                $result['StudentGuardians'] = $this->collStudentGuardians->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collGuardianInvites) {
                $result['GuardianInvites'] = $this->collGuardianInvites->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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

            foreach ($this->getStudentAttendances() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addStudentAttendance($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCourseStudents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCourseStudent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getStudentGuardians() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addStudentGuardian($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getGuardianInvites() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addGuardianInvite($relObj->copy($deepCopy));
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
        if ('StudentAttendance' == $relationName) {
            $this->initStudentAttendances();
        }
        if ('CourseStudent' == $relationName) {
            $this->initCourseStudents();
        }
        if ('StudentGuardian' == $relationName) {
            $this->initStudentGuardians();
        }
        if ('GuardianInvite' == $relationName) {
            $this->initGuardianInvites();
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

                    $collStudentAssignments->getInternalIterator()->rewind();
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
        $studentAssignmentsToDelete = $this->getStudentAssignments(new Criteria(), $con)->diff($studentAssignments);

        $this->studentAssignmentsScheduledForDeletion = unserialize(serialize($studentAssignmentsToDelete));

        foreach ($studentAssignmentsToDelete as $studentAssignmentRemoved) {
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
            $this->studentAssignmentsScheduledForDeletion[]= clone $studentAssignment;
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
     * Clears out the collStudentAttendances collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Student The current object (for fluent API support)
     * @see        addStudentAttendances()
     */
    public function clearStudentAttendances()
    {
        $this->collStudentAttendances = null; // important to set this to null since that means it is uninitialized
        $this->collStudentAttendancesPartial = null;

        return $this;
    }

    /**
     * reset is the collStudentAttendances collection loaded partially
     *
     * @return void
     */
    public function resetPartialStudentAttendances($v = true)
    {
        $this->collStudentAttendancesPartial = $v;
    }

    /**
     * Initializes the collStudentAttendances collection.
     *
     * By default this just sets the collStudentAttendances collection to an empty array (like clearcollStudentAttendances());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initStudentAttendances($overrideExisting = true)
    {
        if (null !== $this->collStudentAttendances && !$overrideExisting) {
            return;
        }
        $this->collStudentAttendances = new PropelObjectCollection();
        $this->collStudentAttendances->setModel('StudentAttendance');
    }

    /**
     * Gets an array of StudentAttendance objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Student is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|StudentAttendance[] List of StudentAttendance objects
     * @throws PropelException
     */
    public function getStudentAttendances($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collStudentAttendancesPartial && !$this->isNew();
        if (null === $this->collStudentAttendances || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collStudentAttendances) {
                // return empty collection
                $this->initStudentAttendances();
            } else {
                $collStudentAttendances = StudentAttendanceQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collStudentAttendancesPartial && count($collStudentAttendances)) {
                      $this->initStudentAttendances(false);

                      foreach($collStudentAttendances as $obj) {
                        if (false == $this->collStudentAttendances->contains($obj)) {
                          $this->collStudentAttendances->append($obj);
                        }
                      }

                      $this->collStudentAttendancesPartial = true;
                    }

                    $collStudentAttendances->getInternalIterator()->rewind();
                    return $collStudentAttendances;
                }

                if($partial && $this->collStudentAttendances) {
                    foreach($this->collStudentAttendances as $obj) {
                        if($obj->isNew()) {
                            $collStudentAttendances[] = $obj;
                        }
                    }
                }

                $this->collStudentAttendances = $collStudentAttendances;
                $this->collStudentAttendancesPartial = false;
            }
        }

        return $this->collStudentAttendances;
    }

    /**
     * Sets a collection of StudentAttendance objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $studentAttendances A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Student The current object (for fluent API support)
     */
    public function setStudentAttendances(PropelCollection $studentAttendances, PropelPDO $con = null)
    {
        $studentAttendancesToDelete = $this->getStudentAttendances(new Criteria(), $con)->diff($studentAttendances);

        $this->studentAttendancesScheduledForDeletion = unserialize(serialize($studentAttendancesToDelete));

        foreach ($studentAttendancesToDelete as $studentAttendanceRemoved) {
            $studentAttendanceRemoved->setStudent(null);
        }

        $this->collStudentAttendances = null;
        foreach ($studentAttendances as $studentAttendance) {
            $this->addStudentAttendance($studentAttendance);
        }

        $this->collStudentAttendances = $studentAttendances;
        $this->collStudentAttendancesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related StudentAttendance objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related StudentAttendance objects.
     * @throws PropelException
     */
    public function countStudentAttendances(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collStudentAttendancesPartial && !$this->isNew();
        if (null === $this->collStudentAttendances || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collStudentAttendances) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getStudentAttendances());
            }
            $query = StudentAttendanceQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByStudent($this)
                ->count($con);
        }

        return count($this->collStudentAttendances);
    }

    /**
     * Method called to associate a StudentAttendance object to this object
     * through the StudentAttendance foreign key attribute.
     *
     * @param    StudentAttendance $l StudentAttendance
     * @return Student The current object (for fluent API support)
     */
    public function addStudentAttendance(StudentAttendance $l)
    {
        if ($this->collStudentAttendances === null) {
            $this->initStudentAttendances();
            $this->collStudentAttendancesPartial = true;
        }
        if (!in_array($l, $this->collStudentAttendances->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddStudentAttendance($l);
        }

        return $this;
    }

    /**
     * @param	StudentAttendance $studentAttendance The studentAttendance object to add.
     */
    protected function doAddStudentAttendance($studentAttendance)
    {
        $this->collStudentAttendances[]= $studentAttendance;
        $studentAttendance->setStudent($this);
    }

    /**
     * @param	StudentAttendance $studentAttendance The studentAttendance object to remove.
     * @return Student The current object (for fluent API support)
     */
    public function removeStudentAttendance($studentAttendance)
    {
        if ($this->getStudentAttendances()->contains($studentAttendance)) {
            $this->collStudentAttendances->remove($this->collStudentAttendances->search($studentAttendance));
            if (null === $this->studentAttendancesScheduledForDeletion) {
                $this->studentAttendancesScheduledForDeletion = clone $this->collStudentAttendances;
                $this->studentAttendancesScheduledForDeletion->clear();
            }
            $this->studentAttendancesScheduledForDeletion[]= clone $studentAttendance;
            $studentAttendance->setStudent(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Student is new, it will return
     * an empty collection; or if this Student has previously
     * been saved, it will retrieve related StudentAttendances from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Student.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|StudentAttendance[] List of StudentAttendance objects
     */
    public function getStudentAttendancesJoinAttendance($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = StudentAttendanceQuery::create(null, $criteria);
        $query->joinWith('Attendance', $join_behavior);

        return $this->getStudentAttendances($query, $con);
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
     * @return Student The current object (for fluent API support)
     */
    public function setCourseStudents(PropelCollection $courseStudents, PropelPDO $con = null)
    {
        $courseStudentsToDelete = $this->getCourseStudents(new Criteria(), $con)->diff($courseStudents);

        $this->courseStudentsScheduledForDeletion = unserialize(serialize($courseStudentsToDelete));

        foreach ($courseStudentsToDelete as $courseStudentRemoved) {
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
            $this->courseStudentsScheduledForDeletion[]= clone $courseStudent;
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
     * Clears out the collStudentGuardians collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Student The current object (for fluent API support)
     * @see        addStudentGuardians()
     */
    public function clearStudentGuardians()
    {
        $this->collStudentGuardians = null; // important to set this to null since that means it is uninitialized
        $this->collStudentGuardiansPartial = null;

        return $this;
    }

    /**
     * reset is the collStudentGuardians collection loaded partially
     *
     * @return void
     */
    public function resetPartialStudentGuardians($v = true)
    {
        $this->collStudentGuardiansPartial = $v;
    }

    /**
     * Initializes the collStudentGuardians collection.
     *
     * By default this just sets the collStudentGuardians collection to an empty array (like clearcollStudentGuardians());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initStudentGuardians($overrideExisting = true)
    {
        if (null !== $this->collStudentGuardians && !$overrideExisting) {
            return;
        }
        $this->collStudentGuardians = new PropelObjectCollection();
        $this->collStudentGuardians->setModel('StudentGuardian');
    }

    /**
     * Gets an array of StudentGuardian objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Student is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|StudentGuardian[] List of StudentGuardian objects
     * @throws PropelException
     */
    public function getStudentGuardians($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collStudentGuardiansPartial && !$this->isNew();
        if (null === $this->collStudentGuardians || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collStudentGuardians) {
                // return empty collection
                $this->initStudentGuardians();
            } else {
                $collStudentGuardians = StudentGuardianQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collStudentGuardiansPartial && count($collStudentGuardians)) {
                      $this->initStudentGuardians(false);

                      foreach($collStudentGuardians as $obj) {
                        if (false == $this->collStudentGuardians->contains($obj)) {
                          $this->collStudentGuardians->append($obj);
                        }
                      }

                      $this->collStudentGuardiansPartial = true;
                    }

                    $collStudentGuardians->getInternalIterator()->rewind();
                    return $collStudentGuardians;
                }

                if($partial && $this->collStudentGuardians) {
                    foreach($this->collStudentGuardians as $obj) {
                        if($obj->isNew()) {
                            $collStudentGuardians[] = $obj;
                        }
                    }
                }

                $this->collStudentGuardians = $collStudentGuardians;
                $this->collStudentGuardiansPartial = false;
            }
        }

        return $this->collStudentGuardians;
    }

    /**
     * Sets a collection of StudentGuardian objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $studentGuardians A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Student The current object (for fluent API support)
     */
    public function setStudentGuardians(PropelCollection $studentGuardians, PropelPDO $con = null)
    {
        $studentGuardiansToDelete = $this->getStudentGuardians(new Criteria(), $con)->diff($studentGuardians);

        $this->studentGuardiansScheduledForDeletion = unserialize(serialize($studentGuardiansToDelete));

        foreach ($studentGuardiansToDelete as $studentGuardianRemoved) {
            $studentGuardianRemoved->setStudent(null);
        }

        $this->collStudentGuardians = null;
        foreach ($studentGuardians as $studentGuardian) {
            $this->addStudentGuardian($studentGuardian);
        }

        $this->collStudentGuardians = $studentGuardians;
        $this->collStudentGuardiansPartial = false;

        return $this;
    }

    /**
     * Returns the number of related StudentGuardian objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related StudentGuardian objects.
     * @throws PropelException
     */
    public function countStudentGuardians(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collStudentGuardiansPartial && !$this->isNew();
        if (null === $this->collStudentGuardians || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collStudentGuardians) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getStudentGuardians());
            }
            $query = StudentGuardianQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByStudent($this)
                ->count($con);
        }

        return count($this->collStudentGuardians);
    }

    /**
     * Method called to associate a StudentGuardian object to this object
     * through the StudentGuardian foreign key attribute.
     *
     * @param    StudentGuardian $l StudentGuardian
     * @return Student The current object (for fluent API support)
     */
    public function addStudentGuardian(StudentGuardian $l)
    {
        if ($this->collStudentGuardians === null) {
            $this->initStudentGuardians();
            $this->collStudentGuardiansPartial = true;
        }
        if (!in_array($l, $this->collStudentGuardians->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddStudentGuardian($l);
        }

        return $this;
    }

    /**
     * @param	StudentGuardian $studentGuardian The studentGuardian object to add.
     */
    protected function doAddStudentGuardian($studentGuardian)
    {
        $this->collStudentGuardians[]= $studentGuardian;
        $studentGuardian->setStudent($this);
    }

    /**
     * @param	StudentGuardian $studentGuardian The studentGuardian object to remove.
     * @return Student The current object (for fluent API support)
     */
    public function removeStudentGuardian($studentGuardian)
    {
        if ($this->getStudentGuardians()->contains($studentGuardian)) {
            $this->collStudentGuardians->remove($this->collStudentGuardians->search($studentGuardian));
            if (null === $this->studentGuardiansScheduledForDeletion) {
                $this->studentGuardiansScheduledForDeletion = clone $this->collStudentGuardians;
                $this->studentGuardiansScheduledForDeletion->clear();
            }
            $this->studentGuardiansScheduledForDeletion[]= clone $studentGuardian;
            $studentGuardian->setStudent(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Student is new, it will return
     * an empty collection; or if this Student has previously
     * been saved, it will retrieve related StudentGuardians from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Student.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|StudentGuardian[] List of StudentGuardian objects
     */
    public function getStudentGuardiansJoinGuardian($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = StudentGuardianQuery::create(null, $criteria);
        $query->joinWith('Guardian', $join_behavior);

        return $this->getStudentGuardians($query, $con);
    }

    /**
     * Clears out the collGuardianInvites collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Student The current object (for fluent API support)
     * @see        addGuardianInvites()
     */
    public function clearGuardianInvites()
    {
        $this->collGuardianInvites = null; // important to set this to null since that means it is uninitialized
        $this->collGuardianInvitesPartial = null;

        return $this;
    }

    /**
     * reset is the collGuardianInvites collection loaded partially
     *
     * @return void
     */
    public function resetPartialGuardianInvites($v = true)
    {
        $this->collGuardianInvitesPartial = $v;
    }

    /**
     * Initializes the collGuardianInvites collection.
     *
     * By default this just sets the collGuardianInvites collection to an empty array (like clearcollGuardianInvites());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initGuardianInvites($overrideExisting = true)
    {
        if (null !== $this->collGuardianInvites && !$overrideExisting) {
            return;
        }
        $this->collGuardianInvites = new PropelObjectCollection();
        $this->collGuardianInvites->setModel('GuardianInvite');
    }

    /**
     * Gets an array of GuardianInvite objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Student is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|GuardianInvite[] List of GuardianInvite objects
     * @throws PropelException
     */
    public function getGuardianInvites($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collGuardianInvitesPartial && !$this->isNew();
        if (null === $this->collGuardianInvites || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collGuardianInvites) {
                // return empty collection
                $this->initGuardianInvites();
            } else {
                $collGuardianInvites = GuardianInviteQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collGuardianInvitesPartial && count($collGuardianInvites)) {
                      $this->initGuardianInvites(false);

                      foreach($collGuardianInvites as $obj) {
                        if (false == $this->collGuardianInvites->contains($obj)) {
                          $this->collGuardianInvites->append($obj);
                        }
                      }

                      $this->collGuardianInvitesPartial = true;
                    }

                    $collGuardianInvites->getInternalIterator()->rewind();
                    return $collGuardianInvites;
                }

                if($partial && $this->collGuardianInvites) {
                    foreach($this->collGuardianInvites as $obj) {
                        if($obj->isNew()) {
                            $collGuardianInvites[] = $obj;
                        }
                    }
                }

                $this->collGuardianInvites = $collGuardianInvites;
                $this->collGuardianInvitesPartial = false;
            }
        }

        return $this->collGuardianInvites;
    }

    /**
     * Sets a collection of GuardianInvite objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $guardianInvites A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Student The current object (for fluent API support)
     */
    public function setGuardianInvites(PropelCollection $guardianInvites, PropelPDO $con = null)
    {
        $guardianInvitesToDelete = $this->getGuardianInvites(new Criteria(), $con)->diff($guardianInvites);

        $this->guardianInvitesScheduledForDeletion = unserialize(serialize($guardianInvitesToDelete));

        foreach ($guardianInvitesToDelete as $guardianInviteRemoved) {
            $guardianInviteRemoved->setStudent(null);
        }

        $this->collGuardianInvites = null;
        foreach ($guardianInvites as $guardianInvite) {
            $this->addGuardianInvite($guardianInvite);
        }

        $this->collGuardianInvites = $guardianInvites;
        $this->collGuardianInvitesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related GuardianInvite objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related GuardianInvite objects.
     * @throws PropelException
     */
    public function countGuardianInvites(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collGuardianInvitesPartial && !$this->isNew();
        if (null === $this->collGuardianInvites || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collGuardianInvites) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getGuardianInvites());
            }
            $query = GuardianInviteQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByStudent($this)
                ->count($con);
        }

        return count($this->collGuardianInvites);
    }

    /**
     * Method called to associate a GuardianInvite object to this object
     * through the GuardianInvite foreign key attribute.
     *
     * @param    GuardianInvite $l GuardianInvite
     * @return Student The current object (for fluent API support)
     */
    public function addGuardianInvite(GuardianInvite $l)
    {
        if ($this->collGuardianInvites === null) {
            $this->initGuardianInvites();
            $this->collGuardianInvitesPartial = true;
        }
        if (!in_array($l, $this->collGuardianInvites->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddGuardianInvite($l);
        }

        return $this;
    }

    /**
     * @param	GuardianInvite $guardianInvite The guardianInvite object to add.
     */
    protected function doAddGuardianInvite($guardianInvite)
    {
        $this->collGuardianInvites[]= $guardianInvite;
        $guardianInvite->setStudent($this);
    }

    /**
     * @param	GuardianInvite $guardianInvite The guardianInvite object to remove.
     * @return Student The current object (for fluent API support)
     */
    public function removeGuardianInvite($guardianInvite)
    {
        if ($this->getGuardianInvites()->contains($guardianInvite)) {
            $this->collGuardianInvites->remove($this->collGuardianInvites->search($guardianInvite));
            if (null === $this->guardianInvitesScheduledForDeletion) {
                $this->guardianInvitesScheduledForDeletion = clone $this->collGuardianInvites;
                $this->guardianInvitesScheduledForDeletion->clear();
            }
            $this->guardianInvitesScheduledForDeletion[]= clone $guardianInvite;
            $guardianInvite->setStudent(null);
        }

        return $this;
    }

    /**
     * Clears out the collAssignments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Student The current object (for fluent API support)
     * @see        addAssignments()
     */
    public function clearAssignments()
    {
        $this->collAssignments = null; // important to set this to null since that means it is uninitialized
        $this->collAssignmentsPartial = null;

        return $this;
    }

    /**
     * Initializes the collAssignments collection.
     *
     * By default this just sets the collAssignments collection to an empty collection (like clearAssignments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initAssignments()
    {
        $this->collAssignments = new PropelObjectCollection();
        $this->collAssignments->setModel('Assignment');
    }

    /**
     * Gets a collection of Assignment objects related by a many-to-many relationship
     * to the current object by way of the student_assignments cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Student is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Assignment[] List of Assignment objects
     */
    public function getAssignments($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collAssignments || null !== $criteria) {
            if ($this->isNew() && null === $this->collAssignments) {
                // return empty collection
                $this->initAssignments();
            } else {
                $collAssignments = AssignmentQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collAssignments;
                }
                $this->collAssignments = $collAssignments;
            }
        }

        return $this->collAssignments;
    }

    /**
     * Sets a collection of Assignment objects related by a many-to-many relationship
     * to the current object by way of the student_assignments cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $assignments A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Student The current object (for fluent API support)
     */
    public function setAssignments(PropelCollection $assignments, PropelPDO $con = null)
    {
        $this->clearAssignments();
        $currentAssignments = $this->getAssignments();

        $this->assignmentsScheduledForDeletion = $currentAssignments->diff($assignments);

        foreach ($assignments as $assignment) {
            if (!$currentAssignments->contains($assignment)) {
                $this->doAddAssignment($assignment);
            }
        }

        $this->collAssignments = $assignments;

        return $this;
    }

    /**
     * Gets the number of Assignment objects related by a many-to-many relationship
     * to the current object by way of the student_assignments cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Assignment objects
     */
    public function countAssignments($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collAssignments || null !== $criteria) {
            if ($this->isNew() && null === $this->collAssignments) {
                return 0;
            } else {
                $query = AssignmentQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByStudent($this)
                    ->count($con);
            }
        } else {
            return count($this->collAssignments);
        }
    }

    /**
     * Associate a Assignment object to this object
     * through the student_assignments cross reference table.
     *
     * @param  Assignment $assignment The StudentAssignment object to relate
     * @return Student The current object (for fluent API support)
     */
    public function addAssignment(Assignment $assignment)
    {
        if ($this->collAssignments === null) {
            $this->initAssignments();
        }
        if (!$this->collAssignments->contains($assignment)) { // only add it if the **same** object is not already associated
            $this->doAddAssignment($assignment);

            $this->collAssignments[]= $assignment;
        }

        return $this;
    }

    /**
     * @param	Assignment $assignment The assignment object to add.
     */
    protected function doAddAssignment($assignment)
    {
        $studentAssignment = new StudentAssignment();
        $studentAssignment->setAssignment($assignment);
        $this->addStudentAssignment($studentAssignment);
    }

    /**
     * Remove a Assignment object to this object
     * through the student_assignments cross reference table.
     *
     * @param Assignment $assignment The StudentAssignment object to relate
     * @return Student The current object (for fluent API support)
     */
    public function removeAssignment(Assignment $assignment)
    {
        if ($this->getAssignments()->contains($assignment)) {
            $this->collAssignments->remove($this->collAssignments->search($assignment));
            if (null === $this->assignmentsScheduledForDeletion) {
                $this->assignmentsScheduledForDeletion = clone $this->collAssignments;
                $this->assignmentsScheduledForDeletion->clear();
            }
            $this->assignmentsScheduledForDeletion[]= $assignment;
        }

        return $this;
    }

    /**
     * Clears out the collCourses collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Student The current object (for fluent API support)
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
     * to the current object by way of the course_students cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Student is new, it will return
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
                    ->filterByStudent($this)
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
     * to the current object by way of the course_students cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $courses A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Student The current object (for fluent API support)
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
     * to the current object by way of the course_students cross-reference table.
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
                    ->filterByStudent($this)
                    ->count($con);
            }
        } else {
            return count($this->collCourses);
        }
    }

    /**
     * Associate a Course object to this object
     * through the course_students cross reference table.
     *
     * @param  Course $course The CourseStudent object to relate
     * @return Student The current object (for fluent API support)
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
        $courseStudent = new CourseStudent();
        $courseStudent->setCourse($course);
        $this->addCourseStudent($courseStudent);
    }

    /**
     * Remove a Course object to this object
     * through the course_students cross reference table.
     *
     * @param Course $course The CourseStudent object to relate
     * @return Student The current object (for fluent API support)
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
     * Clears out the collGuardians collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Student The current object (for fluent API support)
     * @see        addGuardians()
     */
    public function clearGuardians()
    {
        $this->collGuardians = null; // important to set this to null since that means it is uninitialized
        $this->collGuardiansPartial = null;

        return $this;
    }

    /**
     * Initializes the collGuardians collection.
     *
     * By default this just sets the collGuardians collection to an empty collection (like clearGuardians());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initGuardians()
    {
        $this->collGuardians = new PropelObjectCollection();
        $this->collGuardians->setModel('Guardian');
    }

    /**
     * Gets a collection of Guardian objects related by a many-to-many relationship
     * to the current object by way of the student_guardians cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Student is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Guardian[] List of Guardian objects
     */
    public function getGuardians($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collGuardians || null !== $criteria) {
            if ($this->isNew() && null === $this->collGuardians) {
                // return empty collection
                $this->initGuardians();
            } else {
                $collGuardians = GuardianQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collGuardians;
                }
                $this->collGuardians = $collGuardians;
            }
        }

        return $this->collGuardians;
    }

    /**
     * Sets a collection of Guardian objects related by a many-to-many relationship
     * to the current object by way of the student_guardians cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $guardians A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Student The current object (for fluent API support)
     */
    public function setGuardians(PropelCollection $guardians, PropelPDO $con = null)
    {
        $this->clearGuardians();
        $currentGuardians = $this->getGuardians();

        $this->guardiansScheduledForDeletion = $currentGuardians->diff($guardians);

        foreach ($guardians as $guardian) {
            if (!$currentGuardians->contains($guardian)) {
                $this->doAddGuardian($guardian);
            }
        }

        $this->collGuardians = $guardians;

        return $this;
    }

    /**
     * Gets the number of Guardian objects related by a many-to-many relationship
     * to the current object by way of the student_guardians cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Guardian objects
     */
    public function countGuardians($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collGuardians || null !== $criteria) {
            if ($this->isNew() && null === $this->collGuardians) {
                return 0;
            } else {
                $query = GuardianQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByStudent($this)
                    ->count($con);
            }
        } else {
            return count($this->collGuardians);
        }
    }

    /**
     * Associate a Guardian object to this object
     * through the student_guardians cross reference table.
     *
     * @param  Guardian $guardian The StudentGuardian object to relate
     * @return Student The current object (for fluent API support)
     */
    public function addGuardian(Guardian $guardian)
    {
        if ($this->collGuardians === null) {
            $this->initGuardians();
        }
        if (!$this->collGuardians->contains($guardian)) { // only add it if the **same** object is not already associated
            $this->doAddGuardian($guardian);

            $this->collGuardians[]= $guardian;
        }

        return $this;
    }

    /**
     * @param	Guardian $guardian The guardian object to add.
     */
    protected function doAddGuardian($guardian)
    {
        $studentGuardian = new StudentGuardian();
        $studentGuardian->setGuardian($guardian);
        $this->addStudentGuardian($studentGuardian);
    }

    /**
     * Remove a Guardian object to this object
     * through the student_guardians cross reference table.
     *
     * @param Guardian $guardian The StudentGuardian object to relate
     * @return Student The current object (for fluent API support)
     */
    public function removeGuardian(Guardian $guardian)
    {
        if ($this->getGuardians()->contains($guardian)) {
            $this->collGuardians->remove($this->collGuardians->search($guardian));
            if (null === $this->guardiansScheduledForDeletion) {
                $this->guardiansScheduledForDeletion = clone $this->collGuardians;
                $this->guardiansScheduledForDeletion->clear();
            }
            $this->guardiansScheduledForDeletion[]= $guardian;
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
        $this->activities = null;
        $this->interests = null;
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
            if ($this->collStudentAssignments) {
                foreach ($this->collStudentAssignments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collStudentAttendances) {
                foreach ($this->collStudentAttendances as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourseStudents) {
                foreach ($this->collCourseStudents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collStudentGuardians) {
                foreach ($this->collStudentGuardians as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collGuardianInvites) {
                foreach ($this->collGuardianInvites as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAssignments) {
                foreach ($this->collAssignments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCourses) {
                foreach ($this->collCourses as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collGuardians) {
                foreach ($this->collGuardians as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aUser instanceof Persistent) {
              $this->aUser->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collStudentAssignments instanceof PropelCollection) {
            $this->collStudentAssignments->clearIterator();
        }
        $this->collStudentAssignments = null;
        if ($this->collStudentAttendances instanceof PropelCollection) {
            $this->collStudentAttendances->clearIterator();
        }
        $this->collStudentAttendances = null;
        if ($this->collCourseStudents instanceof PropelCollection) {
            $this->collCourseStudents->clearIterator();
        }
        $this->collCourseStudents = null;
        if ($this->collStudentGuardians instanceof PropelCollection) {
            $this->collStudentGuardians->clearIterator();
        }
        $this->collStudentGuardians = null;
        if ($this->collGuardianInvites instanceof PropelCollection) {
            $this->collGuardianInvites->clearIterator();
        }
        $this->collGuardianInvites = null;
        if ($this->collAssignments instanceof PropelCollection) {
            $this->collAssignments->clearIterator();
        }
        $this->collAssignments = null;
        if ($this->collCourses instanceof PropelCollection) {
            $this->collCourses->clearIterator();
        }
        $this->collCourses = null;
        if ($this->collGuardians instanceof PropelCollection) {
            $this->collGuardians->clearIterator();
        }
        $this->collGuardians = null;
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
