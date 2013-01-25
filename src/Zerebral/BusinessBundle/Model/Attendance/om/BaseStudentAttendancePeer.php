<?php

namespace Zerebral\BusinessBundle\Model\Attendance\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Glorpen\PropelEvent\PropelEventBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\PropelEvent\PropelEventBundle\Events\PeerEvent;
use Zerebral\BusinessBundle\Model\Attendance\AttendancePeer;
use Zerebral\BusinessBundle\Model\Attendance\StudentAttendance;
use Zerebral\BusinessBundle\Model\Attendance\StudentAttendancePeer;
use Zerebral\BusinessBundle\Model\Attendance\map\StudentAttendanceTableMap;
use Zerebral\BusinessBundle\Model\User\StudentPeer;

abstract class BaseStudentAttendancePeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'student_attendance';

    /** the related Propel class for this table */
    const OM_CLASS = 'Zerebral\\BusinessBundle\\Model\\Attendance\\StudentAttendance';

    /** the related TableMap class for this table */
    const TM_CLASS = 'StudentAttendanceTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 4;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 4;

    /** the column name for the attendance_id field */
    const ATTENDANCE_ID = 'student_attendance.attendance_id';

    /** the column name for the student_id field */
    const STUDENT_ID = 'student_attendance.student_id';

    /** the column name for the status field */
    const STATUS = 'student_attendance.status';

    /** the column name for the comment field */
    const COMMENT = 'student_attendance.comment';

    /** The enumerated values for the status field */
    const STATUS_PRESENT = 'present';
    const STATUS_TARDY = 'tardy';
    const STATUS_EXCUSED = 'excused';
    const STATUS_ABSENT = 'absent';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identiy map to hold any loaded instances of StudentAttendance objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array StudentAttendance[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. StudentAttendancePeer::$fieldNames[StudentAttendancePeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('AttendanceId', 'StudentId', 'Status', 'Comment', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('attendanceId', 'studentId', 'status', 'comment', ),
        BasePeer::TYPE_COLNAME => array (StudentAttendancePeer::ATTENDANCE_ID, StudentAttendancePeer::STUDENT_ID, StudentAttendancePeer::STATUS, StudentAttendancePeer::COMMENT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ATTENDANCE_ID', 'STUDENT_ID', 'STATUS', 'COMMENT', ),
        BasePeer::TYPE_FIELDNAME => array ('attendance_id', 'student_id', 'status', 'comment', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. StudentAttendancePeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('AttendanceId' => 0, 'StudentId' => 1, 'Status' => 2, 'Comment' => 3, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('attendanceId' => 0, 'studentId' => 1, 'status' => 2, 'comment' => 3, ),
        BasePeer::TYPE_COLNAME => array (StudentAttendancePeer::ATTENDANCE_ID => 0, StudentAttendancePeer::STUDENT_ID => 1, StudentAttendancePeer::STATUS => 2, StudentAttendancePeer::COMMENT => 3, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ATTENDANCE_ID' => 0, 'STUDENT_ID' => 1, 'STATUS' => 2, 'COMMENT' => 3, ),
        BasePeer::TYPE_FIELDNAME => array ('attendance_id' => 0, 'student_id' => 1, 'status' => 2, 'comment' => 3, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, )
    );

    /** The enumerated values for this table */
    protected static $enumValueSets = array(
        StudentAttendancePeer::STATUS => array(
            StudentAttendancePeer::STATUS_PRESENT,
            StudentAttendancePeer::STATUS_TARDY,
            StudentAttendancePeer::STATUS_EXCUSED,
            StudentAttendancePeer::STATUS_ABSENT,
        ),
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = StudentAttendancePeer::getFieldNames($toType);
        $key = isset(StudentAttendancePeer::$fieldKeys[$fromType][$name]) ? StudentAttendancePeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(StudentAttendancePeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, StudentAttendancePeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return StudentAttendancePeer::$fieldNames[$type];
    }

    /**
     * Gets the list of values for all ENUM columns
     * @return array
     */
    public static function getValueSets()
    {
      return StudentAttendancePeer::$enumValueSets;
    }

    /**
     * Gets the list of values for an ENUM column
     *
     * @param string $colname The ENUM column name.
     *
     * @return array list of possible values for the column
     */
    public static function getValueSet($colname)
    {
        $valueSets = StudentAttendancePeer::getValueSets();

        if (!isset($valueSets[$colname])) {
            throw new PropelException(sprintf('Column "%s" has no ValueSet.', $colname));
        }

        return $valueSets[$colname];
    }

    /**
     * Gets the SQL value for the ENUM column value
     *
     * @param string $colname ENUM column name.
     * @param string $enumVal ENUM value.
     *
     * @return int            SQL value
     */
    public static function getSqlValueForEnum($colname, $enumVal)
    {
        $values = StudentAttendancePeer::getValueSet($colname);
        if (!in_array($enumVal, $values)) {
            throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $colname));
        }
        return array_search($enumVal, $values);
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. StudentAttendancePeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(StudentAttendancePeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(StudentAttendancePeer::ATTENDANCE_ID);
            $criteria->addSelectColumn(StudentAttendancePeer::STUDENT_ID);
            $criteria->addSelectColumn(StudentAttendancePeer::STATUS);
            $criteria->addSelectColumn(StudentAttendancePeer::COMMENT);
        } else {
            $criteria->addSelectColumn($alias . '.attendance_id');
            $criteria->addSelectColumn($alias . '.student_id');
            $criteria->addSelectColumn($alias . '.status');
            $criteria->addSelectColumn($alias . '.comment');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(StudentAttendancePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            StudentAttendancePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return                 StudentAttendance
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = StudentAttendancePeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return StudentAttendancePeer::populateObjects(StudentAttendancePeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            StudentAttendancePeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param      StudentAttendance $obj A StudentAttendance object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = serialize(array((string) $obj->getAttendanceId(), (string) $obj->getStudentId()));
            } // if key === null
            StudentAttendancePeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A StudentAttendance object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof StudentAttendance) {
                $key = serialize(array((string) $value->getAttendanceId(), (string) $value->getStudentId()));
            } elseif (is_array($value) && count($value) === 2) {
                // assume we've been passed a primary key
                $key = serialize(array((string) $value[0], (string) $value[1]));
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or StudentAttendance object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(StudentAttendancePeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return   StudentAttendance Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(StudentAttendancePeer::$instances[$key])) {
                return StudentAttendancePeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references)
      {
        foreach (StudentAttendancePeer::$instances as $instance)
        {
          $instance->clearAllReferences(true);
        }
      }
        StudentAttendancePeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to student_attendance
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null && $row[$startcol + 1] === null) {
            return null;
        }

        return serialize(array((string) $row[$startcol], (string) $row[$startcol + 1]));
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return array((int) $row[$startcol], (int) $row[$startcol + 1]);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = StudentAttendancePeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = StudentAttendancePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = StudentAttendancePeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                StudentAttendancePeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (StudentAttendance object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = StudentAttendancePeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = StudentAttendancePeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + StudentAttendancePeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = StudentAttendancePeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            StudentAttendancePeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related Attendance table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAttendance(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(StudentAttendancePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            StudentAttendancePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(StudentAttendancePeer::ATTENDANCE_ID, AttendancePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Student table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinStudent(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(StudentAttendancePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            StudentAttendancePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(StudentAttendancePeer::STUDENT_ID, StudentPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of StudentAttendance objects pre-filled with their Attendance objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of StudentAttendance objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAttendance(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);
        }

        StudentAttendancePeer::addSelectColumns($criteria);
        $startcol = StudentAttendancePeer::NUM_HYDRATE_COLUMNS;
        AttendancePeer::addSelectColumns($criteria);

        $criteria->addJoin(StudentAttendancePeer::ATTENDANCE_ID, AttendancePeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = StudentAttendancePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = StudentAttendancePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = StudentAttendancePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                StudentAttendancePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = AttendancePeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = AttendancePeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = AttendancePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    AttendancePeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (StudentAttendance) to $obj2 (Attendance)
                $obj2->addStudentAttendance($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of StudentAttendance objects pre-filled with their Student objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of StudentAttendance objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinStudent(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);
        }

        StudentAttendancePeer::addSelectColumns($criteria);
        $startcol = StudentAttendancePeer::NUM_HYDRATE_COLUMNS;
        StudentPeer::addSelectColumns($criteria);

        $criteria->addJoin(StudentAttendancePeer::STUDENT_ID, StudentPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = StudentAttendancePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = StudentAttendancePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = StudentAttendancePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                StudentAttendancePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = StudentPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = StudentPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = StudentPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    StudentPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (StudentAttendance) to $obj2 (Student)
                $obj2->addStudentAttendance($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(StudentAttendancePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            StudentAttendancePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(StudentAttendancePeer::ATTENDANCE_ID, AttendancePeer::ID, $join_behavior);

        $criteria->addJoin(StudentAttendancePeer::STUDENT_ID, StudentPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Selects a collection of StudentAttendance objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of StudentAttendance objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);
        }

        StudentAttendancePeer::addSelectColumns($criteria);
        $startcol2 = StudentAttendancePeer::NUM_HYDRATE_COLUMNS;

        AttendancePeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + AttendancePeer::NUM_HYDRATE_COLUMNS;

        StudentPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + StudentPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(StudentAttendancePeer::ATTENDANCE_ID, AttendancePeer::ID, $join_behavior);

        $criteria->addJoin(StudentAttendancePeer::STUDENT_ID, StudentPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = StudentAttendancePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = StudentAttendancePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = StudentAttendancePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                StudentAttendancePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Attendance rows

            $key2 = AttendancePeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = AttendancePeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = AttendancePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    AttendancePeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (StudentAttendance) to the collection in $obj2 (Attendance)
                $obj2->addStudentAttendance($obj1);
            } // if joined row not null

            // Add objects for joined Student rows

            $key3 = StudentPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = StudentPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = StudentPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    StudentPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (StudentAttendance) to the collection in $obj3 (Student)
                $obj3->addStudentAttendance($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Attendance table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptAttendance(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(StudentAttendancePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            StudentAttendancePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(StudentAttendancePeer::STUDENT_ID, StudentPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Student table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptStudent(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(StudentAttendancePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            StudentAttendancePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(StudentAttendancePeer::ATTENDANCE_ID, AttendancePeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of StudentAttendance objects pre-filled with all related objects except Attendance.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of StudentAttendance objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptAttendance(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);
        }

        StudentAttendancePeer::addSelectColumns($criteria);
        $startcol2 = StudentAttendancePeer::NUM_HYDRATE_COLUMNS;

        StudentPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + StudentPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(StudentAttendancePeer::STUDENT_ID, StudentPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = StudentAttendancePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = StudentAttendancePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = StudentAttendancePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                StudentAttendancePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Student rows

                $key2 = StudentPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = StudentPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = StudentPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    StudentPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (StudentAttendance) to the collection in $obj2 (Student)
                $obj2->addStudentAttendance($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of StudentAttendance objects pre-filled with all related objects except Student.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of StudentAttendance objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptStudent(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);
        }

        StudentAttendancePeer::addSelectColumns($criteria);
        $startcol2 = StudentAttendancePeer::NUM_HYDRATE_COLUMNS;

        AttendancePeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + AttendancePeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(StudentAttendancePeer::ATTENDANCE_ID, AttendancePeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = StudentAttendancePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = StudentAttendancePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = StudentAttendancePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                StudentAttendancePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Attendance rows

                $key2 = AttendancePeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = AttendancePeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = AttendancePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    AttendancePeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (StudentAttendance) to the collection in $obj2 (Attendance)
                $obj2->addStudentAttendance($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(StudentAttendancePeer::DATABASE_NAME)->getTable(StudentAttendancePeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseStudentAttendancePeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseStudentAttendancePeer::TABLE_NAME)) {
        $dbMap->addTableObject(new StudentAttendanceTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass()
    {
        return StudentAttendancePeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a StudentAttendance or Criteria object.
     *
     * @param      mixed $values Criteria or StudentAttendance object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from StudentAttendance object
        }


        // Set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a StudentAttendance or Criteria object.
     *
     * @param      mixed $values Criteria or StudentAttendance object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(StudentAttendancePeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(StudentAttendancePeer::ATTENDANCE_ID);
            $value = $criteria->remove(StudentAttendancePeer::ATTENDANCE_ID);
            if ($value) {
                $selectCriteria->add(StudentAttendancePeer::ATTENDANCE_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(StudentAttendancePeer::TABLE_NAME);
            }

            $comparison = $criteria->getComparison(StudentAttendancePeer::STUDENT_ID);
            $value = $criteria->remove(StudentAttendancePeer::STUDENT_ID);
            if ($value) {
                $selectCriteria->add(StudentAttendancePeer::STUDENT_ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(StudentAttendancePeer::TABLE_NAME);
            }

        } else { // $values is StudentAttendance object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the student_attendance table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(StudentAttendancePeer::TABLE_NAME, $con, StudentAttendancePeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            StudentAttendancePeer::clearInstancePool();
            StudentAttendancePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a StudentAttendance or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or StudentAttendance object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            StudentAttendancePeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof StudentAttendance) { // it's a model object
            // invalidate the cache for this single object
            StudentAttendancePeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(StudentAttendancePeer::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            foreach ($values as $value) {
                $criterion = $criteria->getNewCriterion(StudentAttendancePeer::ATTENDANCE_ID, $value[0]);
                $criterion->addAnd($criteria->getNewCriterion(StudentAttendancePeer::STUDENT_ID, $value[1]));
                $criteria->addOr($criterion);
                // we can invalidate the cache for this single PK
                StudentAttendancePeer::removeInstanceFromPool($value);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(StudentAttendancePeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            StudentAttendancePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given StudentAttendance object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      StudentAttendance $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(StudentAttendancePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(StudentAttendancePeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(StudentAttendancePeer::DATABASE_NAME, StudentAttendancePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param   int $attendance_id
     * @param   int $student_id
     * @param      PropelPDO $con
     * @return   StudentAttendance
     */
    public static function retrieveByPK($attendance_id, $student_id, PropelPDO $con = null) {
        $_instancePoolKey = serialize(array((string) $attendance_id, (string) $student_id));
         if (null !== ($obj = StudentAttendancePeer::getInstanceFromPool($_instancePoolKey))) {
             return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(StudentAttendancePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $criteria = new Criteria(StudentAttendancePeer::DATABASE_NAME);
        $criteria->add(StudentAttendancePeer::ATTENDANCE_ID, $attendance_id);
        $criteria->add(StudentAttendancePeer::STUDENT_ID, $student_id);
        $v = StudentAttendancePeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
} // BaseStudentAttendancePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseStudentAttendancePeer::buildTableMap();

EventDispatcherProxy::trigger(array('construct','peer.construct'), new PeerEvent('Zerebral\BusinessBundle\Model\Attendance\om\BaseStudentAttendancePeer'));
