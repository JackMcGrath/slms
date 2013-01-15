<?php

namespace Zerebral\BusinessBundle\Model\User\om;

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
use Zerebral\BusinessBundle\Model\Feed\FeedComment;
use Zerebral\BusinessBundle\Model\Feed\FeedCommentQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\Feed\FeedItemQuery;
use Zerebral\BusinessBundle\Model\File\File;
use Zerebral\BusinessBundle\Model\File\FileQuery;
use Zerebral\BusinessBundle\Model\Notification\Notification;
use Zerebral\BusinessBundle\Model\Notification\NotificationQuery;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\StudentQuery;
use Zerebral\BusinessBundle\Model\User\Teacher;
use Zerebral\BusinessBundle\Model\User\TeacherQuery;
use Zerebral\BusinessBundle\Model\User\User;
use Zerebral\BusinessBundle\Model\User\UserPeer;
use Zerebral\BusinessBundle\Model\User\UserQuery;

abstract class BaseUser extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Zerebral\\BusinessBundle\\Model\\User\\UserPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        UserPeer
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
     * The value for the role field.
     * @var        string
     */
    protected $role;

    /**
     * The value for the first_name field.
     * @var        string
     */
    protected $first_name;

    /**
     * The value for the last_name field.
     * @var        string
     */
    protected $last_name;

    /**
     * The value for the salutation field.
     * @var        string
     */
    protected $salutation;

    /**
     * The value for the birthday field.
     * @var        string
     */
    protected $birthday;

    /**
     * The value for the gender field.
     * @var        string
     */
    protected $gender;

    /**
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the password field.
     * @var        string
     */
    protected $password;

    /**
     * The value for the salt field.
     * @var        string
     */
    protected $salt;

    /**
     * The value for the avatar_id field.
     * @var        int
     */
    protected $avatar_id;

    /**
     * The value for the is_active field.
     * Note: this column has a database default value of: true
     * @var        boolean
     */
    protected $is_active;

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
     * @var        File
     */
    protected $aAvatar;

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
     * @var        PropelObjectCollection|Notification[] Collection to store aggregation of Notification objects.
     */
    protected $collNotificationsRelatedByCreatedBy;
    protected $collNotificationsRelatedByCreatedByPartial;

    /**
     * @var        PropelObjectCollection|Notification[] Collection to store aggregation of Notification objects.
     */
    protected $collNotificationsRelatedByUserId;
    protected $collNotificationsRelatedByUserIdPartial;

    /**
     * @var        PropelObjectCollection|Student[] Collection to store aggregation of Student objects.
     */
    protected $collStudents;
    protected $collStudentsPartial;

    /**
     * @var        PropelObjectCollection|Teacher[] Collection to store aggregation of Teacher objects.
     */
    protected $collTeachers;
    protected $collTeachersPartial;

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
    protected $feedItemsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $feedCommentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $notificationsRelatedByCreatedByScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $notificationsRelatedByUserIdScheduledForDeletion = null;

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
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->is_active = true;
    }

    /**
     * Initializes internal state of BaseUser object.
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
     * Get the [role] column value.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get the [first_name] column value.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Get the [last_name] column value.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Get the [salutation] column value.
     *
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * Get the [optionally formatted] temporal [birthday] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *				 If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getBirthday($format = null)
    {
        if ($this->birthday === null) {
            return null;
        }

        if ($this->birthday === '0000-00-00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->birthday);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->birthday, true), $x);
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
     * Get the [gender] column value.
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Get the [email] column value.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the [password] column value.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get the [salt] column value.
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Get the [avatar_id] column value.
     *
     * @return int
     */
    public function getAvatarId()
    {
        return $this->avatar_id;
    }

    /**
     * Get the [is_active] column value.
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
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
     * @return User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = UserPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [role] column.
     *
     * @param string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setRole($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->role !== $v) {
            $this->role = $v;
            $this->modifiedColumns[] = UserPeer::ROLE;
        }


        return $this;
    } // setRole()

    /**
     * Set the value of [first_name] column.
     *
     * @param string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setFirstName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->first_name !== $v) {
            $this->first_name = $v;
            $this->modifiedColumns[] = UserPeer::FIRST_NAME;
        }


        return $this;
    } // setFirstName()

    /**
     * Set the value of [last_name] column.
     *
     * @param string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setLastName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->last_name !== $v) {
            $this->last_name = $v;
            $this->modifiedColumns[] = UserPeer::LAST_NAME;
        }


        return $this;
    } // setLastName()

    /**
     * Set the value of [salutation] column.
     *
     * @param string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setSalutation($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->salutation !== $v) {
            $this->salutation = $v;
            $this->modifiedColumns[] = UserPeer::SALUTATION;
        }


        return $this;
    } // setSalutation()

    /**
     * Sets the value of [birthday] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return User The current object (for fluent API support)
     */
    public function setBirthday($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->birthday !== null || $dt !== null) {
            $currentDateAsString = ($this->birthday !== null && $tmpDt = new DateTime($this->birthday)) ? $tmpDt->format('Y-m-d') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->birthday = $newDateAsString;
                $this->modifiedColumns[] = UserPeer::BIRTHDAY;
            }
        } // if either are not null


        return $this;
    } // setBirthday()

    /**
     * Set the value of [gender] column.
     *
     * @param string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setGender($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->gender !== $v) {
            $this->gender = $v;
            $this->modifiedColumns[] = UserPeer::GENDER;
        }


        return $this;
    } // setGender()

    /**
     * Set the value of [email] column.
     *
     * @param string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = UserPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [password] column.
     *
     * @param string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->password !== $v) {
            $this->password = $v;
            $this->modifiedColumns[] = UserPeer::PASSWORD;
        }


        return $this;
    } // setPassword()

    /**
     * Set the value of [salt] column.
     *
     * @param string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setSalt($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->salt !== $v) {
            $this->salt = $v;
            $this->modifiedColumns[] = UserPeer::SALT;
        }


        return $this;
    } // setSalt()

    /**
     * Set the value of [avatar_id] column.
     *
     * @param int $v new value
     * @return User The current object (for fluent API support)
     */
    public function setAvatarId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->avatar_id !== $v) {
            $this->avatar_id = $v;
            $this->modifiedColumns[] = UserPeer::AVATAR_ID;
        }

        if ($this->aAvatar !== null && $this->aAvatar->getId() !== $v) {
            $this->aAvatar = null;
        }


        return $this;
    } // setAvatarId()

    /**
     * Sets the value of the [is_active] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return User The current object (for fluent API support)
     */
    public function setIsActive($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_active !== $v) {
            $this->is_active = $v;
            $this->modifiedColumns[] = UserPeer::IS_ACTIVE;
        }


        return $this;
    } // setIsActive()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return User The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = UserPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return User The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = UserPeer::UPDATED_AT;
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
            if ($this->is_active !== true) {
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
            $this->role = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->first_name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->last_name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->salutation = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->birthday = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->gender = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->email = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->password = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->salt = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->avatar_id = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
            $this->is_active = ($row[$startcol + 11] !== null) ? (boolean) $row[$startcol + 11] : null;
            $this->created_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->updated_at = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 14; // 14 = UserPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating User object", $e);
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

        if ($this->aAvatar !== null && $this->avatar_id !== $this->aAvatar->getId()) {
            $this->aAvatar = null;
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
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = UserPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAvatar = null;
            $this->collFeedItems = null;

            $this->collFeedComments = null;

            $this->collNotificationsRelatedByCreatedBy = null;

            $this->collNotificationsRelatedByUserId = null;

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
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = UserQuery::create()
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
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                UserPeer::addInstanceToPool($this);
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

            if ($this->aAvatar !== null) {
                if ($this->aAvatar->isModified() || $this->aAvatar->isNew()) {
                    $affectedRows += $this->aAvatar->save($con);
                }
                $this->setAvatar($this->aAvatar);
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

            if ($this->notificationsRelatedByCreatedByScheduledForDeletion !== null) {
                if (!$this->notificationsRelatedByCreatedByScheduledForDeletion->isEmpty()) {
                    foreach ($this->notificationsRelatedByCreatedByScheduledForDeletion as $notificationRelatedByCreatedBy) {
                        // need to save related object because we set the relation to null
                        $notificationRelatedByCreatedBy->save($con);
                    }
                    $this->notificationsRelatedByCreatedByScheduledForDeletion = null;
                }
            }

            if ($this->collNotificationsRelatedByCreatedBy !== null) {
                foreach ($this->collNotificationsRelatedByCreatedBy as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->notificationsRelatedByUserIdScheduledForDeletion !== null) {
                if (!$this->notificationsRelatedByUserIdScheduledForDeletion->isEmpty()) {
                    NotificationQuery::create()
                        ->filterByPrimaryKeys($this->notificationsRelatedByUserIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->notificationsRelatedByUserIdScheduledForDeletion = null;
                }
            }

            if ($this->collNotificationsRelatedByUserId !== null) {
                foreach ($this->collNotificationsRelatedByUserId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->studentsScheduledForDeletion !== null) {
                if (!$this->studentsScheduledForDeletion->isEmpty()) {
                    StudentQuery::create()
                        ->filterByPrimaryKeys($this->studentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->studentsScheduledForDeletion = null;
                }
            }

            if ($this->collStudents !== null) {
                foreach ($this->collStudents as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->teachersScheduledForDeletion !== null) {
                if (!$this->teachersScheduledForDeletion->isEmpty()) {
                    TeacherQuery::create()
                        ->filterByPrimaryKeys($this->teachersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->teachersScheduledForDeletion = null;
                }
            }

            if ($this->collTeachers !== null) {
                foreach ($this->collTeachers as $referrerFK) {
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

        $this->modifiedColumns[] = UserPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(UserPeer::ROLE)) {
            $modifiedColumns[':p' . $index++]  = '`role`';
        }
        if ($this->isColumnModified(UserPeer::FIRST_NAME)) {
            $modifiedColumns[':p' . $index++]  = '`first_name`';
        }
        if ($this->isColumnModified(UserPeer::LAST_NAME)) {
            $modifiedColumns[':p' . $index++]  = '`last_name`';
        }
        if ($this->isColumnModified(UserPeer::SALUTATION)) {
            $modifiedColumns[':p' . $index++]  = '`salutation`';
        }
        if ($this->isColumnModified(UserPeer::BIRTHDAY)) {
            $modifiedColumns[':p' . $index++]  = '`birthday`';
        }
        if ($this->isColumnModified(UserPeer::GENDER)) {
            $modifiedColumns[':p' . $index++]  = '`gender`';
        }
        if ($this->isColumnModified(UserPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = '`email`';
        }
        if ($this->isColumnModified(UserPeer::PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = '`password`';
        }
        if ($this->isColumnModified(UserPeer::SALT)) {
            $modifiedColumns[':p' . $index++]  = '`salt`';
        }
        if ($this->isColumnModified(UserPeer::AVATAR_ID)) {
            $modifiedColumns[':p' . $index++]  = '`avatar_id`';
        }
        if ($this->isColumnModified(UserPeer::IS_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = '`is_active`';
        }
        if ($this->isColumnModified(UserPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(UserPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `users` (%s) VALUES (%s)',
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
                    case '`role`':
                        $stmt->bindValue($identifier, $this->role, PDO::PARAM_STR);
                        break;
                    case '`first_name`':
                        $stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
                        break;
                    case '`last_name`':
                        $stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
                        break;
                    case '`salutation`':
                        $stmt->bindValue($identifier, $this->salutation, PDO::PARAM_STR);
                        break;
                    case '`birthday`':
                        $stmt->bindValue($identifier, $this->birthday, PDO::PARAM_STR);
                        break;
                    case '`gender`':
                        $stmt->bindValue($identifier, $this->gender, PDO::PARAM_STR);
                        break;
                    case '`email`':
                        $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case '`password`':
                        $stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
                        break;
                    case '`salt`':
                        $stmt->bindValue($identifier, $this->salt, PDO::PARAM_STR);
                        break;
                    case '`avatar_id`':
                        $stmt->bindValue($identifier, $this->avatar_id, PDO::PARAM_INT);
                        break;
                    case '`is_active`':
                        $stmt->bindValue($identifier, (int) $this->is_active, PDO::PARAM_INT);
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

            if ($this->aAvatar !== null) {
                if (!$this->aAvatar->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAvatar->getValidationFailures());
                }
            }


            if (($retval = UserPeer::doValidate($this, $columns)) !== true) {
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

                if ($this->collNotificationsRelatedByCreatedBy !== null) {
                    foreach ($this->collNotificationsRelatedByCreatedBy as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collNotificationsRelatedByUserId !== null) {
                    foreach ($this->collNotificationsRelatedByUserId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collStudents !== null) {
                    foreach ($this->collStudents as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collTeachers !== null) {
                    foreach ($this->collTeachers as $referrerFK) {
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
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getRole();
                break;
            case 2:
                return $this->getFirstName();
                break;
            case 3:
                return $this->getLastName();
                break;
            case 4:
                return $this->getSalutation();
                break;
            case 5:
                return $this->getBirthday();
                break;
            case 6:
                return $this->getGender();
                break;
            case 7:
                return $this->getEmail();
                break;
            case 8:
                return $this->getPassword();
                break;
            case 9:
                return $this->getSalt();
                break;
            case 10:
                return $this->getAvatarId();
                break;
            case 11:
                return $this->getIsActive();
                break;
            case 12:
                return $this->getCreatedAt();
                break;
            case 13:
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
        if (isset($alreadyDumpedObjects['User'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->getPrimaryKey()] = true;
        $keys = UserPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getRole(),
            $keys[2] => $this->getFirstName(),
            $keys[3] => $this->getLastName(),
            $keys[4] => $this->getSalutation(),
            $keys[5] => $this->getBirthday(),
            $keys[6] => $this->getGender(),
            $keys[7] => $this->getEmail(),
            $keys[8] => $this->getPassword(),
            $keys[9] => $this->getSalt(),
            $keys[10] => $this->getAvatarId(),
            $keys[11] => $this->getIsActive(),
            $keys[12] => $this->getCreatedAt(),
            $keys[13] => $this->getUpdatedAt(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aAvatar) {
                $result['Avatar'] = $this->aAvatar->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collFeedItems) {
                $result['FeedItems'] = $this->collFeedItems->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collFeedComments) {
                $result['FeedComments'] = $this->collFeedComments->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collNotificationsRelatedByCreatedBy) {
                $result['NotificationsRelatedByCreatedBy'] = $this->collNotificationsRelatedByCreatedBy->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collNotificationsRelatedByUserId) {
                $result['NotificationsRelatedByUserId'] = $this->collNotificationsRelatedByUserId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collStudents) {
                $result['Students'] = $this->collStudents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTeachers) {
                $result['Teachers'] = $this->collTeachers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setRole($value);
                break;
            case 2:
                $this->setFirstName($value);
                break;
            case 3:
                $this->setLastName($value);
                break;
            case 4:
                $this->setSalutation($value);
                break;
            case 5:
                $this->setBirthday($value);
                break;
            case 6:
                $this->setGender($value);
                break;
            case 7:
                $this->setEmail($value);
                break;
            case 8:
                $this->setPassword($value);
                break;
            case 9:
                $this->setSalt($value);
                break;
            case 10:
                $this->setAvatarId($value);
                break;
            case 11:
                $this->setIsActive($value);
                break;
            case 12:
                $this->setCreatedAt($value);
                break;
            case 13:
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
        $keys = UserPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setRole($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setFirstName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setLastName($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setSalutation($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setBirthday($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setGender($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setEmail($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setPassword($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setSalt($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setAvatarId($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setIsActive($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setCreatedAt($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setUpdatedAt($arr[$keys[13]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserPeer::DATABASE_NAME);

        if ($this->isColumnModified(UserPeer::ID)) $criteria->add(UserPeer::ID, $this->id);
        if ($this->isColumnModified(UserPeer::ROLE)) $criteria->add(UserPeer::ROLE, $this->role);
        if ($this->isColumnModified(UserPeer::FIRST_NAME)) $criteria->add(UserPeer::FIRST_NAME, $this->first_name);
        if ($this->isColumnModified(UserPeer::LAST_NAME)) $criteria->add(UserPeer::LAST_NAME, $this->last_name);
        if ($this->isColumnModified(UserPeer::SALUTATION)) $criteria->add(UserPeer::SALUTATION, $this->salutation);
        if ($this->isColumnModified(UserPeer::BIRTHDAY)) $criteria->add(UserPeer::BIRTHDAY, $this->birthday);
        if ($this->isColumnModified(UserPeer::GENDER)) $criteria->add(UserPeer::GENDER, $this->gender);
        if ($this->isColumnModified(UserPeer::EMAIL)) $criteria->add(UserPeer::EMAIL, $this->email);
        if ($this->isColumnModified(UserPeer::PASSWORD)) $criteria->add(UserPeer::PASSWORD, $this->password);
        if ($this->isColumnModified(UserPeer::SALT)) $criteria->add(UserPeer::SALT, $this->salt);
        if ($this->isColumnModified(UserPeer::AVATAR_ID)) $criteria->add(UserPeer::AVATAR_ID, $this->avatar_id);
        if ($this->isColumnModified(UserPeer::IS_ACTIVE)) $criteria->add(UserPeer::IS_ACTIVE, $this->is_active);
        if ($this->isColumnModified(UserPeer::CREATED_AT)) $criteria->add(UserPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(UserPeer::UPDATED_AT)) $criteria->add(UserPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(UserPeer::DATABASE_NAME);
        $criteria->add(UserPeer::ID, $this->id);

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
     * @param object $copyObj An object of User (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setRole($this->getRole());
        $copyObj->setFirstName($this->getFirstName());
        $copyObj->setLastName($this->getLastName());
        $copyObj->setSalutation($this->getSalutation());
        $copyObj->setBirthday($this->getBirthday());
        $copyObj->setGender($this->getGender());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setPassword($this->getPassword());
        $copyObj->setSalt($this->getSalt());
        $copyObj->setAvatarId($this->getAvatarId());
        $copyObj->setIsActive($this->getIsActive());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

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

            foreach ($this->getNotificationsRelatedByCreatedBy() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNotificationRelatedByCreatedBy($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getNotificationsRelatedByUserId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNotificationRelatedByUserId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getStudents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addStudent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTeachers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTeacher($relObj->copy($deepCopy));
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
     * @return User Clone of current object.
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
     * @return UserPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new UserPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a File object.
     *
     * @param             File $v
     * @return User The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAvatar(File $v = null)
    {
        if ($v === null) {
            $this->setAvatarId(NULL);
        } else {
            $this->setAvatarId($v->getId());
        }

        $this->aAvatar = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the File object, it will not be re-added.
        if ($v !== null) {
            $v->addUser($this);
        }


        return $this;
    }


    /**
     * Get the associated File object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return File The associated File object.
     * @throws PropelException
     */
    public function getAvatar(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAvatar === null && ($this->avatar_id !== null) && $doQuery) {
            $this->aAvatar = FileQuery::create()->findPk($this->avatar_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAvatar->addUsers($this);
             */
        }

        return $this->aAvatar;
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
        if ('NotificationRelatedByCreatedBy' == $relationName) {
            $this->initNotificationsRelatedByCreatedBy();
        }
        if ('NotificationRelatedByUserId' == $relationName) {
            $this->initNotificationsRelatedByUserId();
        }
        if ('Student' == $relationName) {
            $this->initStudents();
        }
        if ('Teacher' == $relationName) {
            $this->initTeachers();
        }
    }

    /**
     * Clears out the collFeedItems collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
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
     * If this User is new, it will return
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
                    ->filterByUser($this)
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
     * @return User The current object (for fluent API support)
     */
    public function setFeedItems(PropelCollection $feedItems, PropelPDO $con = null)
    {
        $feedItemsToDelete = $this->getFeedItems(new Criteria(), $con)->diff($feedItems);

        $this->feedItemsScheduledForDeletion = unserialize(serialize($feedItemsToDelete));

        foreach ($feedItemsToDelete as $feedItemRemoved) {
            $feedItemRemoved->setUser(null);
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
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collFeedItems);
    }

    /**
     * Method called to associate a FeedItem object to this object
     * through the FeedItem foreign key attribute.
     *
     * @param    FeedItem $l FeedItem
     * @return User The current object (for fluent API support)
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
        $feedItem->setUser($this);
    }

    /**
     * @param	FeedItem $feedItem The feedItem object to remove.
     * @return User The current object (for fluent API support)
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
            $feedItem->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
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
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
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
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related FeedItems from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
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
     * Clears out the collFeedComments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
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
     * If this User is new, it will return
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
                    ->filterByUser($this)
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

                    $collFeedComments->getInternalIterator()->rewind();
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
     * @return User The current object (for fluent API support)
     */
    public function setFeedComments(PropelCollection $feedComments, PropelPDO $con = null)
    {
        $feedCommentsToDelete = $this->getFeedComments(new Criteria(), $con)->diff($feedComments);

        $this->feedCommentsScheduledForDeletion = unserialize(serialize($feedCommentsToDelete));

        foreach ($feedCommentsToDelete as $feedCommentRemoved) {
            $feedCommentRemoved->setUser(null);
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
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collFeedComments);
    }

    /**
     * Method called to associate a FeedComment object to this object
     * through the FeedComment foreign key attribute.
     *
     * @param    FeedComment $l FeedComment
     * @return User The current object (for fluent API support)
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
        $feedComment->setUser($this);
    }

    /**
     * @param	FeedComment $feedComment The feedComment object to remove.
     * @return User The current object (for fluent API support)
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
            $feedComment->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related FeedComments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
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
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related FeedComments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|FeedComment[] List of FeedComment objects
     */
    public function getFeedCommentsJoinFeedContent($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = FeedCommentQuery::create(null, $criteria);
        $query->joinWith('FeedContent', $join_behavior);

        return $this->getFeedComments($query, $con);
    }

    /**
     * Clears out the collNotificationsRelatedByCreatedBy collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addNotificationsRelatedByCreatedBy()
     */
    public function clearNotificationsRelatedByCreatedBy()
    {
        $this->collNotificationsRelatedByCreatedBy = null; // important to set this to null since that means it is uninitialized
        $this->collNotificationsRelatedByCreatedByPartial = null;

        return $this;
    }

    /**
     * reset is the collNotificationsRelatedByCreatedBy collection loaded partially
     *
     * @return void
     */
    public function resetPartialNotificationsRelatedByCreatedBy($v = true)
    {
        $this->collNotificationsRelatedByCreatedByPartial = $v;
    }

    /**
     * Initializes the collNotificationsRelatedByCreatedBy collection.
     *
     * By default this just sets the collNotificationsRelatedByCreatedBy collection to an empty array (like clearcollNotificationsRelatedByCreatedBy());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNotificationsRelatedByCreatedBy($overrideExisting = true)
    {
        if (null !== $this->collNotificationsRelatedByCreatedBy && !$overrideExisting) {
            return;
        }
        $this->collNotificationsRelatedByCreatedBy = new PropelObjectCollection();
        $this->collNotificationsRelatedByCreatedBy->setModel('Notification');
    }

    /**
     * Gets an array of Notification objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Notification[] List of Notification objects
     * @throws PropelException
     */
    public function getNotificationsRelatedByCreatedBy($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collNotificationsRelatedByCreatedByPartial && !$this->isNew();
        if (null === $this->collNotificationsRelatedByCreatedBy || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNotificationsRelatedByCreatedBy) {
                // return empty collection
                $this->initNotificationsRelatedByCreatedBy();
            } else {
                $collNotificationsRelatedByCreatedBy = NotificationQuery::create(null, $criteria)
                    ->filterByUserRelatedByCreatedBy($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collNotificationsRelatedByCreatedByPartial && count($collNotificationsRelatedByCreatedBy)) {
                      $this->initNotificationsRelatedByCreatedBy(false);

                      foreach($collNotificationsRelatedByCreatedBy as $obj) {
                        if (false == $this->collNotificationsRelatedByCreatedBy->contains($obj)) {
                          $this->collNotificationsRelatedByCreatedBy->append($obj);
                        }
                      }

                      $this->collNotificationsRelatedByCreatedByPartial = true;
                    }

                    $collNotificationsRelatedByCreatedBy->getInternalIterator()->rewind();
                    return $collNotificationsRelatedByCreatedBy;
                }

                if($partial && $this->collNotificationsRelatedByCreatedBy) {
                    foreach($this->collNotificationsRelatedByCreatedBy as $obj) {
                        if($obj->isNew()) {
                            $collNotificationsRelatedByCreatedBy[] = $obj;
                        }
                    }
                }

                $this->collNotificationsRelatedByCreatedBy = $collNotificationsRelatedByCreatedBy;
                $this->collNotificationsRelatedByCreatedByPartial = false;
            }
        }

        return $this->collNotificationsRelatedByCreatedBy;
    }

    /**
     * Sets a collection of NotificationRelatedByCreatedBy objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $notificationsRelatedByCreatedBy A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setNotificationsRelatedByCreatedBy(PropelCollection $notificationsRelatedByCreatedBy, PropelPDO $con = null)
    {
        $notificationsRelatedByCreatedByToDelete = $this->getNotificationsRelatedByCreatedBy(new Criteria(), $con)->diff($notificationsRelatedByCreatedBy);

        $this->notificationsRelatedByCreatedByScheduledForDeletion = unserialize(serialize($notificationsRelatedByCreatedByToDelete));

        foreach ($notificationsRelatedByCreatedByToDelete as $notificationRelatedByCreatedByRemoved) {
            $notificationRelatedByCreatedByRemoved->setUserRelatedByCreatedBy(null);
        }

        $this->collNotificationsRelatedByCreatedBy = null;
        foreach ($notificationsRelatedByCreatedBy as $notificationRelatedByCreatedBy) {
            $this->addNotificationRelatedByCreatedBy($notificationRelatedByCreatedBy);
        }

        $this->collNotificationsRelatedByCreatedBy = $notificationsRelatedByCreatedBy;
        $this->collNotificationsRelatedByCreatedByPartial = false;

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
    public function countNotificationsRelatedByCreatedBy(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collNotificationsRelatedByCreatedByPartial && !$this->isNew();
        if (null === $this->collNotificationsRelatedByCreatedBy || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotificationsRelatedByCreatedBy) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getNotificationsRelatedByCreatedBy());
            }
            $query = NotificationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUserRelatedByCreatedBy($this)
                ->count($con);
        }

        return count($this->collNotificationsRelatedByCreatedBy);
    }

    /**
     * Method called to associate a Notification object to this object
     * through the Notification foreign key attribute.
     *
     * @param    Notification $l Notification
     * @return User The current object (for fluent API support)
     */
    public function addNotificationRelatedByCreatedBy(Notification $l)
    {
        if ($this->collNotificationsRelatedByCreatedBy === null) {
            $this->initNotificationsRelatedByCreatedBy();
            $this->collNotificationsRelatedByCreatedByPartial = true;
        }
        if (!in_array($l, $this->collNotificationsRelatedByCreatedBy->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddNotificationRelatedByCreatedBy($l);
        }

        return $this;
    }

    /**
     * @param	NotificationRelatedByCreatedBy $notificationRelatedByCreatedBy The notificationRelatedByCreatedBy object to add.
     */
    protected function doAddNotificationRelatedByCreatedBy($notificationRelatedByCreatedBy)
    {
        $this->collNotificationsRelatedByCreatedBy[]= $notificationRelatedByCreatedBy;
        $notificationRelatedByCreatedBy->setUserRelatedByCreatedBy($this);
    }

    /**
     * @param	NotificationRelatedByCreatedBy $notificationRelatedByCreatedBy The notificationRelatedByCreatedBy object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeNotificationRelatedByCreatedBy($notificationRelatedByCreatedBy)
    {
        if ($this->getNotificationsRelatedByCreatedBy()->contains($notificationRelatedByCreatedBy)) {
            $this->collNotificationsRelatedByCreatedBy->remove($this->collNotificationsRelatedByCreatedBy->search($notificationRelatedByCreatedBy));
            if (null === $this->notificationsRelatedByCreatedByScheduledForDeletion) {
                $this->notificationsRelatedByCreatedByScheduledForDeletion = clone $this->collNotificationsRelatedByCreatedBy;
                $this->notificationsRelatedByCreatedByScheduledForDeletion->clear();
            }
            $this->notificationsRelatedByCreatedByScheduledForDeletion[]= $notificationRelatedByCreatedBy;
            $notificationRelatedByCreatedBy->setUserRelatedByCreatedBy(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related NotificationsRelatedByCreatedBy from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Notification[] List of Notification objects
     */
    public function getNotificationsRelatedByCreatedByJoinAssignment($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = NotificationQuery::create(null, $criteria);
        $query->joinWith('Assignment', $join_behavior);

        return $this->getNotificationsRelatedByCreatedBy($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related NotificationsRelatedByCreatedBy from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Notification[] List of Notification objects
     */
    public function getNotificationsRelatedByCreatedByJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = NotificationQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getNotificationsRelatedByCreatedBy($query, $con);
    }

    /**
     * Clears out the collNotificationsRelatedByUserId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addNotificationsRelatedByUserId()
     */
    public function clearNotificationsRelatedByUserId()
    {
        $this->collNotificationsRelatedByUserId = null; // important to set this to null since that means it is uninitialized
        $this->collNotificationsRelatedByUserIdPartial = null;

        return $this;
    }

    /**
     * reset is the collNotificationsRelatedByUserId collection loaded partially
     *
     * @return void
     */
    public function resetPartialNotificationsRelatedByUserId($v = true)
    {
        $this->collNotificationsRelatedByUserIdPartial = $v;
    }

    /**
     * Initializes the collNotificationsRelatedByUserId collection.
     *
     * By default this just sets the collNotificationsRelatedByUserId collection to an empty array (like clearcollNotificationsRelatedByUserId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNotificationsRelatedByUserId($overrideExisting = true)
    {
        if (null !== $this->collNotificationsRelatedByUserId && !$overrideExisting) {
            return;
        }
        $this->collNotificationsRelatedByUserId = new PropelObjectCollection();
        $this->collNotificationsRelatedByUserId->setModel('Notification');
    }

    /**
     * Gets an array of Notification objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Notification[] List of Notification objects
     * @throws PropelException
     */
    public function getNotificationsRelatedByUserId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collNotificationsRelatedByUserIdPartial && !$this->isNew();
        if (null === $this->collNotificationsRelatedByUserId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNotificationsRelatedByUserId) {
                // return empty collection
                $this->initNotificationsRelatedByUserId();
            } else {
                $collNotificationsRelatedByUserId = NotificationQuery::create(null, $criteria)
                    ->filterByUserRelatedByUserId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collNotificationsRelatedByUserIdPartial && count($collNotificationsRelatedByUserId)) {
                      $this->initNotificationsRelatedByUserId(false);

                      foreach($collNotificationsRelatedByUserId as $obj) {
                        if (false == $this->collNotificationsRelatedByUserId->contains($obj)) {
                          $this->collNotificationsRelatedByUserId->append($obj);
                        }
                      }

                      $this->collNotificationsRelatedByUserIdPartial = true;
                    }

                    $collNotificationsRelatedByUserId->getInternalIterator()->rewind();
                    return $collNotificationsRelatedByUserId;
                }

                if($partial && $this->collNotificationsRelatedByUserId) {
                    foreach($this->collNotificationsRelatedByUserId as $obj) {
                        if($obj->isNew()) {
                            $collNotificationsRelatedByUserId[] = $obj;
                        }
                    }
                }

                $this->collNotificationsRelatedByUserId = $collNotificationsRelatedByUserId;
                $this->collNotificationsRelatedByUserIdPartial = false;
            }
        }

        return $this->collNotificationsRelatedByUserId;
    }

    /**
     * Sets a collection of NotificationRelatedByUserId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $notificationsRelatedByUserId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setNotificationsRelatedByUserId(PropelCollection $notificationsRelatedByUserId, PropelPDO $con = null)
    {
        $notificationsRelatedByUserIdToDelete = $this->getNotificationsRelatedByUserId(new Criteria(), $con)->diff($notificationsRelatedByUserId);

        $this->notificationsRelatedByUserIdScheduledForDeletion = unserialize(serialize($notificationsRelatedByUserIdToDelete));

        foreach ($notificationsRelatedByUserIdToDelete as $notificationRelatedByUserIdRemoved) {
            $notificationRelatedByUserIdRemoved->setUserRelatedByUserId(null);
        }

        $this->collNotificationsRelatedByUserId = null;
        foreach ($notificationsRelatedByUserId as $notificationRelatedByUserId) {
            $this->addNotificationRelatedByUserId($notificationRelatedByUserId);
        }

        $this->collNotificationsRelatedByUserId = $notificationsRelatedByUserId;
        $this->collNotificationsRelatedByUserIdPartial = false;

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
    public function countNotificationsRelatedByUserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collNotificationsRelatedByUserIdPartial && !$this->isNew();
        if (null === $this->collNotificationsRelatedByUserId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotificationsRelatedByUserId) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getNotificationsRelatedByUserId());
            }
            $query = NotificationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUserRelatedByUserId($this)
                ->count($con);
        }

        return count($this->collNotificationsRelatedByUserId);
    }

    /**
     * Method called to associate a Notification object to this object
     * through the Notification foreign key attribute.
     *
     * @param    Notification $l Notification
     * @return User The current object (for fluent API support)
     */
    public function addNotificationRelatedByUserId(Notification $l)
    {
        if ($this->collNotificationsRelatedByUserId === null) {
            $this->initNotificationsRelatedByUserId();
            $this->collNotificationsRelatedByUserIdPartial = true;
        }
        if (!in_array($l, $this->collNotificationsRelatedByUserId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddNotificationRelatedByUserId($l);
        }

        return $this;
    }

    /**
     * @param	NotificationRelatedByUserId $notificationRelatedByUserId The notificationRelatedByUserId object to add.
     */
    protected function doAddNotificationRelatedByUserId($notificationRelatedByUserId)
    {
        $this->collNotificationsRelatedByUserId[]= $notificationRelatedByUserId;
        $notificationRelatedByUserId->setUserRelatedByUserId($this);
    }

    /**
     * @param	NotificationRelatedByUserId $notificationRelatedByUserId The notificationRelatedByUserId object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeNotificationRelatedByUserId($notificationRelatedByUserId)
    {
        if ($this->getNotificationsRelatedByUserId()->contains($notificationRelatedByUserId)) {
            $this->collNotificationsRelatedByUserId->remove($this->collNotificationsRelatedByUserId->search($notificationRelatedByUserId));
            if (null === $this->notificationsRelatedByUserIdScheduledForDeletion) {
                $this->notificationsRelatedByUserIdScheduledForDeletion = clone $this->collNotificationsRelatedByUserId;
                $this->notificationsRelatedByUserIdScheduledForDeletion->clear();
            }
            $this->notificationsRelatedByUserIdScheduledForDeletion[]= clone $notificationRelatedByUserId;
            $notificationRelatedByUserId->setUserRelatedByUserId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related NotificationsRelatedByUserId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Notification[] List of Notification objects
     */
    public function getNotificationsRelatedByUserIdJoinAssignment($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = NotificationQuery::create(null, $criteria);
        $query->joinWith('Assignment', $join_behavior);

        return $this->getNotificationsRelatedByUserId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related NotificationsRelatedByUserId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Notification[] List of Notification objects
     */
    public function getNotificationsRelatedByUserIdJoinCourse($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = NotificationQuery::create(null, $criteria);
        $query->joinWith('Course', $join_behavior);

        return $this->getNotificationsRelatedByUserId($query, $con);
    }

    /**
     * Clears out the collStudents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addStudents()
     */
    public function clearStudents()
    {
        $this->collStudents = null; // important to set this to null since that means it is uninitialized
        $this->collStudentsPartial = null;

        return $this;
    }

    /**
     * reset is the collStudents collection loaded partially
     *
     * @return void
     */
    public function resetPartialStudents($v = true)
    {
        $this->collStudentsPartial = $v;
    }

    /**
     * Initializes the collStudents collection.
     *
     * By default this just sets the collStudents collection to an empty array (like clearcollStudents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initStudents($overrideExisting = true)
    {
        if (null !== $this->collStudents && !$overrideExisting) {
            return;
        }
        $this->collStudents = new PropelObjectCollection();
        $this->collStudents->setModel('Student');
    }

    /**
     * Gets an array of Student objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Student[] List of Student objects
     * @throws PropelException
     */
    public function getStudents($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collStudentsPartial && !$this->isNew();
        if (null === $this->collStudents || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collStudents) {
                // return empty collection
                $this->initStudents();
            } else {
                $collStudents = StudentQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collStudentsPartial && count($collStudents)) {
                      $this->initStudents(false);

                      foreach($collStudents as $obj) {
                        if (false == $this->collStudents->contains($obj)) {
                          $this->collStudents->append($obj);
                        }
                      }

                      $this->collStudentsPartial = true;
                    }

                    $collStudents->getInternalIterator()->rewind();
                    return $collStudents;
                }

                if($partial && $this->collStudents) {
                    foreach($this->collStudents as $obj) {
                        if($obj->isNew()) {
                            $collStudents[] = $obj;
                        }
                    }
                }

                $this->collStudents = $collStudents;
                $this->collStudentsPartial = false;
            }
        }

        return $this->collStudents;
    }

    /**
     * Sets a collection of Student objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $students A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setStudents(PropelCollection $students, PropelPDO $con = null)
    {
        $studentsToDelete = $this->getStudents(new Criteria(), $con)->diff($students);

        $this->studentsScheduledForDeletion = unserialize(serialize($studentsToDelete));

        foreach ($studentsToDelete as $studentRemoved) {
            $studentRemoved->setUser(null);
        }

        $this->collStudents = null;
        foreach ($students as $student) {
            $this->addStudent($student);
        }

        $this->collStudents = $students;
        $this->collStudentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Student objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Student objects.
     * @throws PropelException
     */
    public function countStudents(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collStudentsPartial && !$this->isNew();
        if (null === $this->collStudents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collStudents) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getStudents());
            }
            $query = StudentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collStudents);
    }

    /**
     * Method called to associate a Student object to this object
     * through the Student foreign key attribute.
     *
     * @param    Student $l Student
     * @return User The current object (for fluent API support)
     */
    public function addStudent(Student $l)
    {
        if ($this->collStudents === null) {
            $this->initStudents();
            $this->collStudentsPartial = true;
        }
        if (!in_array($l, $this->collStudents->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddStudent($l);
        }

        return $this;
    }

    /**
     * @param	Student $student The student object to add.
     */
    protected function doAddStudent($student)
    {
        $this->collStudents[]= $student;
        $student->setUser($this);
    }

    /**
     * @param	Student $student The student object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeStudent($student)
    {
        if ($this->getStudents()->contains($student)) {
            $this->collStudents->remove($this->collStudents->search($student));
            if (null === $this->studentsScheduledForDeletion) {
                $this->studentsScheduledForDeletion = clone $this->collStudents;
                $this->studentsScheduledForDeletion->clear();
            }
            $this->studentsScheduledForDeletion[]= clone $student;
            $student->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collTeachers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addTeachers()
     */
    public function clearTeachers()
    {
        $this->collTeachers = null; // important to set this to null since that means it is uninitialized
        $this->collTeachersPartial = null;

        return $this;
    }

    /**
     * reset is the collTeachers collection loaded partially
     *
     * @return void
     */
    public function resetPartialTeachers($v = true)
    {
        $this->collTeachersPartial = $v;
    }

    /**
     * Initializes the collTeachers collection.
     *
     * By default this just sets the collTeachers collection to an empty array (like clearcollTeachers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTeachers($overrideExisting = true)
    {
        if (null !== $this->collTeachers && !$overrideExisting) {
            return;
        }
        $this->collTeachers = new PropelObjectCollection();
        $this->collTeachers->setModel('Teacher');
    }

    /**
     * Gets an array of Teacher objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Teacher[] List of Teacher objects
     * @throws PropelException
     */
    public function getTeachers($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTeachersPartial && !$this->isNew();
        if (null === $this->collTeachers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTeachers) {
                // return empty collection
                $this->initTeachers();
            } else {
                $collTeachers = TeacherQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTeachersPartial && count($collTeachers)) {
                      $this->initTeachers(false);

                      foreach($collTeachers as $obj) {
                        if (false == $this->collTeachers->contains($obj)) {
                          $this->collTeachers->append($obj);
                        }
                      }

                      $this->collTeachersPartial = true;
                    }

                    $collTeachers->getInternalIterator()->rewind();
                    return $collTeachers;
                }

                if($partial && $this->collTeachers) {
                    foreach($this->collTeachers as $obj) {
                        if($obj->isNew()) {
                            $collTeachers[] = $obj;
                        }
                    }
                }

                $this->collTeachers = $collTeachers;
                $this->collTeachersPartial = false;
            }
        }

        return $this->collTeachers;
    }

    /**
     * Sets a collection of Teacher objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $teachers A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setTeachers(PropelCollection $teachers, PropelPDO $con = null)
    {
        $teachersToDelete = $this->getTeachers(new Criteria(), $con)->diff($teachers);

        $this->teachersScheduledForDeletion = unserialize(serialize($teachersToDelete));

        foreach ($teachersToDelete as $teacherRemoved) {
            $teacherRemoved->setUser(null);
        }

        $this->collTeachers = null;
        foreach ($teachers as $teacher) {
            $this->addTeacher($teacher);
        }

        $this->collTeachers = $teachers;
        $this->collTeachersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Teacher objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Teacher objects.
     * @throws PropelException
     */
    public function countTeachers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTeachersPartial && !$this->isNew();
        if (null === $this->collTeachers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTeachers) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getTeachers());
            }
            $query = TeacherQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collTeachers);
    }

    /**
     * Method called to associate a Teacher object to this object
     * through the Teacher foreign key attribute.
     *
     * @param    Teacher $l Teacher
     * @return User The current object (for fluent API support)
     */
    public function addTeacher(Teacher $l)
    {
        if ($this->collTeachers === null) {
            $this->initTeachers();
            $this->collTeachersPartial = true;
        }
        if (!in_array($l, $this->collTeachers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTeacher($l);
        }

        return $this;
    }

    /**
     * @param	Teacher $teacher The teacher object to add.
     */
    protected function doAddTeacher($teacher)
    {
        $this->collTeachers[]= $teacher;
        $teacher->setUser($this);
    }

    /**
     * @param	Teacher $teacher The teacher object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeTeacher($teacher)
    {
        if ($this->getTeachers()->contains($teacher)) {
            $this->collTeachers->remove($this->collTeachers->search($teacher));
            if (null === $this->teachersScheduledForDeletion) {
                $this->teachersScheduledForDeletion = clone $this->collTeachers;
                $this->teachersScheduledForDeletion->clear();
            }
            $this->teachersScheduledForDeletion[]= clone $teacher;
            $teacher->setUser(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->role = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->salutation = null;
        $this->birthday = null;
        $this->gender = null;
        $this->email = null;
        $this->password = null;
        $this->salt = null;
        $this->avatar_id = null;
        $this->is_active = null;
        $this->created_at = null;
        $this->updated_at = null;
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
            if ($this->collNotificationsRelatedByCreatedBy) {
                foreach ($this->collNotificationsRelatedByCreatedBy as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collNotificationsRelatedByUserId) {
                foreach ($this->collNotificationsRelatedByUserId as $o) {
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
            if ($this->aAvatar instanceof Persistent) {
              $this->aAvatar->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collFeedItems instanceof PropelCollection) {
            $this->collFeedItems->clearIterator();
        }
        $this->collFeedItems = null;
        if ($this->collFeedComments instanceof PropelCollection) {
            $this->collFeedComments->clearIterator();
        }
        $this->collFeedComments = null;
        if ($this->collNotificationsRelatedByCreatedBy instanceof PropelCollection) {
            $this->collNotificationsRelatedByCreatedBy->clearIterator();
        }
        $this->collNotificationsRelatedByCreatedBy = null;
        if ($this->collNotificationsRelatedByUserId instanceof PropelCollection) {
            $this->collNotificationsRelatedByUserId->clearIterator();
        }
        $this->collNotificationsRelatedByUserId = null;
        if ($this->collStudents instanceof PropelCollection) {
            $this->collStudents->clearIterator();
        }
        $this->collStudents = null;
        if ($this->collTeachers instanceof PropelCollection) {
            $this->collTeachers->clearIterator();
        }
        $this->collTeachers = null;
        $this->aAvatar = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserPeer::DEFAULT_STRING_FORMAT);
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
