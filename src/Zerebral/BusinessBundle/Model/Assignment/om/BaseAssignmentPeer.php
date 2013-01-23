<?php

namespace Zerebral\BusinessBundle\Model\Assignment\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Glorpen\PropelEvent\PropelEventBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\PropelEvent\PropelEventBundle\Events\PeerEvent;
use Zerebral\BusinessBundle\Model\Assignment\Assignment;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryPeer;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentFilePeer;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentPeer;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer;
use Zerebral\BusinessBundle\Model\Assignment\map\AssignmentTableMap;
use Zerebral\BusinessBundle\Model\Course\CoursePeer;
use Zerebral\BusinessBundle\Model\Feed\FeedItemPeer;
use Zerebral\BusinessBundle\Model\Notification\NotificationPeer;
use Zerebral\BusinessBundle\Model\User\TeacherPeer;

abstract class BaseAssignmentPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'assignments';

    /** the related Propel class for this table */
    const OM_CLASS = 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment';

    /** the related TableMap class for this table */
    const TM_CLASS = 'AssignmentTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 10;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 10;

    /** the column name for the id field */
    const ID = 'assignments.id';

    /** the column name for the teacher_id field */
    const TEACHER_ID = 'assignments.teacher_id';

    /** the column name for the course_id field */
    const COURSE_ID = 'assignments.course_id';

    /** the column name for the assignment_category_id field */
    const ASSIGNMENT_CATEGORY_ID = 'assignments.assignment_category_id';

    /** the column name for the name field */
    const NAME = 'assignments.name';

    /** the column name for the description field */
    const DESCRIPTION = 'assignments.description';

    /** the column name for the max_points field */
    const MAX_POINTS = 'assignments.max_points';

    /** the column name for the grade_type field */
    const GRADE_TYPE = 'assignments.grade_type';

    /** the column name for the threshold field */
    const THRESHOLD = 'assignments.threshold';

    /** the column name for the due_at field */
    const DUE_AT = 'assignments.due_at';

    /** The enumerated values for the grade_type field */
    const GRADE_TYPE_NUMERIC = 'numeric';
    const GRADE_TYPE_PASS = 'pass';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identiy map to hold any loaded instances of Assignment objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Assignment[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. AssignmentPeer::$fieldNames[AssignmentPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'TeacherId', 'CourseId', 'AssignmentCategoryId', 'Name', 'Description', 'MaxPoints', 'GradeType', 'Threshold', 'DueAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'teacherId', 'courseId', 'assignmentCategoryId', 'name', 'description', 'maxPoints', 'gradeType', 'threshold', 'dueAt', ),
        BasePeer::TYPE_COLNAME => array (AssignmentPeer::ID, AssignmentPeer::TEACHER_ID, AssignmentPeer::COURSE_ID, AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentPeer::NAME, AssignmentPeer::DESCRIPTION, AssignmentPeer::MAX_POINTS, AssignmentPeer::GRADE_TYPE, AssignmentPeer::THRESHOLD, AssignmentPeer::DUE_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'TEACHER_ID', 'COURSE_ID', 'ASSIGNMENT_CATEGORY_ID', 'NAME', 'DESCRIPTION', 'MAX_POINTS', 'GRADE_TYPE', 'THRESHOLD', 'DUE_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'teacher_id', 'course_id', 'assignment_category_id', 'name', 'description', 'max_points', 'grade_type', 'threshold', 'due_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. AssignmentPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'TeacherId' => 1, 'CourseId' => 2, 'AssignmentCategoryId' => 3, 'Name' => 4, 'Description' => 5, 'MaxPoints' => 6, 'GradeType' => 7, 'Threshold' => 8, 'DueAt' => 9, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'teacherId' => 1, 'courseId' => 2, 'assignmentCategoryId' => 3, 'name' => 4, 'description' => 5, 'maxPoints' => 6, 'gradeType' => 7, 'threshold' => 8, 'dueAt' => 9, ),
        BasePeer::TYPE_COLNAME => array (AssignmentPeer::ID => 0, AssignmentPeer::TEACHER_ID => 1, AssignmentPeer::COURSE_ID => 2, AssignmentPeer::ASSIGNMENT_CATEGORY_ID => 3, AssignmentPeer::NAME => 4, AssignmentPeer::DESCRIPTION => 5, AssignmentPeer::MAX_POINTS => 6, AssignmentPeer::GRADE_TYPE => 7, AssignmentPeer::THRESHOLD => 8, AssignmentPeer::DUE_AT => 9, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'TEACHER_ID' => 1, 'COURSE_ID' => 2, 'ASSIGNMENT_CATEGORY_ID' => 3, 'NAME' => 4, 'DESCRIPTION' => 5, 'MAX_POINTS' => 6, 'GRADE_TYPE' => 7, 'THRESHOLD' => 8, 'DUE_AT' => 9, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'teacher_id' => 1, 'course_id' => 2, 'assignment_category_id' => 3, 'name' => 4, 'description' => 5, 'max_points' => 6, 'grade_type' => 7, 'threshold' => 8, 'due_at' => 9, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /** The enumerated values for this table */
    protected static $enumValueSets = array(
        AssignmentPeer::GRADE_TYPE => array(
            AssignmentPeer::GRADE_TYPE_NUMERIC,
            AssignmentPeer::GRADE_TYPE_PASS,
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
        $toNames = AssignmentPeer::getFieldNames($toType);
        $key = isset(AssignmentPeer::$fieldKeys[$fromType][$name]) ? AssignmentPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(AssignmentPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, AssignmentPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return AssignmentPeer::$fieldNames[$type];
    }

    /**
     * Gets the list of values for all ENUM columns
     * @return array
     */
    public static function getValueSets()
    {
      return AssignmentPeer::$enumValueSets;
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
        $valueSets = AssignmentPeer::getValueSets();

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
        $values = AssignmentPeer::getValueSet($colname);
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
     * @param      string $column The column name for current table. (i.e. AssignmentPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(AssignmentPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(AssignmentPeer::ID);
            $criteria->addSelectColumn(AssignmentPeer::TEACHER_ID);
            $criteria->addSelectColumn(AssignmentPeer::COURSE_ID);
            $criteria->addSelectColumn(AssignmentPeer::ASSIGNMENT_CATEGORY_ID);
            $criteria->addSelectColumn(AssignmentPeer::NAME);
            $criteria->addSelectColumn(AssignmentPeer::DESCRIPTION);
            $criteria->addSelectColumn(AssignmentPeer::MAX_POINTS);
            $criteria->addSelectColumn(AssignmentPeer::GRADE_TYPE);
            $criteria->addSelectColumn(AssignmentPeer::THRESHOLD);
            $criteria->addSelectColumn(AssignmentPeer::DUE_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.teacher_id');
            $criteria->addSelectColumn($alias . '.course_id');
            $criteria->addSelectColumn($alias . '.assignment_category_id');
            $criteria->addSelectColumn($alias . '.name');
            $criteria->addSelectColumn($alias . '.description');
            $criteria->addSelectColumn($alias . '.max_points');
            $criteria->addSelectColumn($alias . '.grade_type');
            $criteria->addSelectColumn($alias . '.threshold');
            $criteria->addSelectColumn($alias . '.due_at');
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
        $criteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AssignmentPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Assignment
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = AssignmentPeer::doSelect($critcopy, $con);
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
        return AssignmentPeer::populateObjects(AssignmentPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            AssignmentPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

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
     * @param      Assignment $obj A Assignment object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            AssignmentPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A Assignment object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Assignment) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Assignment object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(AssignmentPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return   Assignment Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(AssignmentPeer::$instances[$key])) {
                return AssignmentPeer::$instances[$key];
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
        foreach (AssignmentPeer::$instances as $instance)
        {
          $instance->clearAllReferences(true);
        }
      }
        AssignmentPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to assignments
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in AssignmentFilePeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        AssignmentFilePeer::clearInstancePool();
        // Invalidate objects in StudentAssignmentPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        StudentAssignmentPeer::clearInstancePool();
        // Invalidate objects in FeedItemPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        FeedItemPeer::clearInstancePool();
        // Invalidate objects in NotificationPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        NotificationPeer::clearInstancePool();
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
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
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

        return (int) $row[$startcol];
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
        $cls = AssignmentPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = AssignmentPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = AssignmentPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                AssignmentPeer::addInstanceToPool($obj, $key);
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
     * @return array (Assignment object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = AssignmentPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = AssignmentPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + AssignmentPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = AssignmentPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            AssignmentPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related Teacher table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinTeacher(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AssignmentPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AssignmentPeer::TEACHER_ID, TeacherPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related Course table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCourse(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AssignmentPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AssignmentPeer::COURSE_ID, CoursePeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related AssignmentCategory table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAssignmentCategory(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AssignmentPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentCategoryPeer::ID, $join_behavior);

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
     * Selects a collection of Assignment objects pre-filled with their Teacher objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Assignment objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinTeacher(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AssignmentPeer::DATABASE_NAME);
        }

        AssignmentPeer::addSelectColumns($criteria);
        $startcol = AssignmentPeer::NUM_HYDRATE_COLUMNS;
        TeacherPeer::addSelectColumns($criteria);

        $criteria->addJoin(AssignmentPeer::TEACHER_ID, TeacherPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AssignmentPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AssignmentPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = AssignmentPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AssignmentPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = TeacherPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = TeacherPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = TeacherPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    TeacherPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Assignment) to $obj2 (Teacher)
                $obj2->addAssignment($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Assignment objects pre-filled with their Course objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Assignment objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCourse(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AssignmentPeer::DATABASE_NAME);
        }

        AssignmentPeer::addSelectColumns($criteria);
        $startcol = AssignmentPeer::NUM_HYDRATE_COLUMNS;
        CoursePeer::addSelectColumns($criteria);

        $criteria->addJoin(AssignmentPeer::COURSE_ID, CoursePeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AssignmentPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AssignmentPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = AssignmentPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AssignmentPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CoursePeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CoursePeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CoursePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CoursePeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Assignment) to $obj2 (Course)
                $obj2->addAssignment($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Assignment objects pre-filled with their AssignmentCategory objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Assignment objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAssignmentCategory(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AssignmentPeer::DATABASE_NAME);
        }

        AssignmentPeer::addSelectColumns($criteria);
        $startcol = AssignmentPeer::NUM_HYDRATE_COLUMNS;
        AssignmentCategoryPeer::addSelectColumns($criteria);

        $criteria->addJoin(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentCategoryPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AssignmentPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AssignmentPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = AssignmentPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AssignmentPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = AssignmentCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = AssignmentCategoryPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = AssignmentCategoryPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    AssignmentCategoryPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Assignment) to $obj2 (AssignmentCategory)
                $obj2->addAssignment($obj1);

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
        $criteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AssignmentPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AssignmentPeer::TEACHER_ID, TeacherPeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::COURSE_ID, CoursePeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentCategoryPeer::ID, $join_behavior);

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
     * Selects a collection of Assignment objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Assignment objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AssignmentPeer::DATABASE_NAME);
        }

        AssignmentPeer::addSelectColumns($criteria);
        $startcol2 = AssignmentPeer::NUM_HYDRATE_COLUMNS;

        TeacherPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TeacherPeer::NUM_HYDRATE_COLUMNS;

        CoursePeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CoursePeer::NUM_HYDRATE_COLUMNS;

        AssignmentCategoryPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + AssignmentCategoryPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AssignmentPeer::TEACHER_ID, TeacherPeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::COURSE_ID, CoursePeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentCategoryPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AssignmentPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AssignmentPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AssignmentPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AssignmentPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Teacher rows

            $key2 = TeacherPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = TeacherPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = TeacherPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TeacherPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Assignment) to the collection in $obj2 (Teacher)
                $obj2->addAssignment($obj1);
            } // if joined row not null

            // Add objects for joined Course rows

            $key3 = CoursePeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = CoursePeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = CoursePeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CoursePeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (Assignment) to the collection in $obj3 (Course)
                $obj3->addAssignment($obj1);
            } // if joined row not null

            // Add objects for joined AssignmentCategory rows

            $key4 = AssignmentCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol4);
            if ($key4 !== null) {
                $obj4 = AssignmentCategoryPeer::getInstanceFromPool($key4);
                if (!$obj4) {

                    $cls = AssignmentCategoryPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    AssignmentCategoryPeer::addInstanceToPool($obj4, $key4);
                } // if obj4 loaded

                // Add the $obj1 (Assignment) to the collection in $obj4 (AssignmentCategory)
                $obj4->addAssignment($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Teacher table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptTeacher(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AssignmentPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AssignmentPeer::COURSE_ID, CoursePeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentCategoryPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related Course table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCourse(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AssignmentPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AssignmentPeer::TEACHER_ID, TeacherPeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentCategoryPeer::ID, $join_behavior);

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
     * Returns the number of rows matching criteria, joining the related AssignmentCategory table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptAssignmentCategory(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            AssignmentPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(AssignmentPeer::TEACHER_ID, TeacherPeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::COURSE_ID, CoursePeer::ID, $join_behavior);

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
     * Selects a collection of Assignment objects pre-filled with all related objects except Teacher.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Assignment objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptTeacher(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AssignmentPeer::DATABASE_NAME);
        }

        AssignmentPeer::addSelectColumns($criteria);
        $startcol2 = AssignmentPeer::NUM_HYDRATE_COLUMNS;

        CoursePeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CoursePeer::NUM_HYDRATE_COLUMNS;

        AssignmentCategoryPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + AssignmentCategoryPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AssignmentPeer::COURSE_ID, CoursePeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentCategoryPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AssignmentPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AssignmentPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AssignmentPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AssignmentPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Course rows

                $key2 = CoursePeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CoursePeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CoursePeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CoursePeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Assignment) to the collection in $obj2 (Course)
                $obj2->addAssignment($obj1);

            } // if joined row is not null

                // Add objects for joined AssignmentCategory rows

                $key3 = AssignmentCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = AssignmentCategoryPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = AssignmentCategoryPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    AssignmentCategoryPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Assignment) to the collection in $obj3 (AssignmentCategory)
                $obj3->addAssignment($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Assignment objects pre-filled with all related objects except Course.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Assignment objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCourse(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AssignmentPeer::DATABASE_NAME);
        }

        AssignmentPeer::addSelectColumns($criteria);
        $startcol2 = AssignmentPeer::NUM_HYDRATE_COLUMNS;

        TeacherPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TeacherPeer::NUM_HYDRATE_COLUMNS;

        AssignmentCategoryPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + AssignmentCategoryPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AssignmentPeer::TEACHER_ID, TeacherPeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, AssignmentCategoryPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AssignmentPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AssignmentPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AssignmentPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AssignmentPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Teacher rows

                $key2 = TeacherPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = TeacherPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = TeacherPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TeacherPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Assignment) to the collection in $obj2 (Teacher)
                $obj2->addAssignment($obj1);

            } // if joined row is not null

                // Add objects for joined AssignmentCategory rows

                $key3 = AssignmentCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = AssignmentCategoryPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = AssignmentCategoryPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    AssignmentCategoryPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Assignment) to the collection in $obj3 (AssignmentCategory)
                $obj3->addAssignment($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Assignment objects pre-filled with all related objects except AssignmentCategory.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Assignment objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptAssignmentCategory(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(AssignmentPeer::DATABASE_NAME);
        }

        AssignmentPeer::addSelectColumns($criteria);
        $startcol2 = AssignmentPeer::NUM_HYDRATE_COLUMNS;

        TeacherPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TeacherPeer::NUM_HYDRATE_COLUMNS;

        CoursePeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CoursePeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(AssignmentPeer::TEACHER_ID, TeacherPeer::ID, $join_behavior);

        $criteria->addJoin(AssignmentPeer::COURSE_ID, CoursePeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = AssignmentPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = AssignmentPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = AssignmentPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                AssignmentPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Teacher rows

                $key2 = TeacherPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = TeacherPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = TeacherPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TeacherPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Assignment) to the collection in $obj2 (Teacher)
                $obj2->addAssignment($obj1);

            } // if joined row is not null

                // Add objects for joined Course rows

                $key3 = CoursePeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CoursePeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CoursePeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CoursePeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Assignment) to the collection in $obj3 (Course)
                $obj3->addAssignment($obj1);

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
        return Propel::getDatabaseMap(AssignmentPeer::DATABASE_NAME)->getTable(AssignmentPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseAssignmentPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseAssignmentPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new AssignmentTableMap());
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
        return AssignmentPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Assignment or Criteria object.
     *
     * @param      mixed $values Criteria or Assignment object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Assignment object
        }

        if ($criteria->containsKey(AssignmentPeer::ID) && $criteria->keyContainsValue(AssignmentPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.AssignmentPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a Assignment or Criteria object.
     *
     * @param      mixed $values Criteria or Assignment object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(AssignmentPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(AssignmentPeer::ID);
            $value = $criteria->remove(AssignmentPeer::ID);
            if ($value) {
                $selectCriteria->add(AssignmentPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(AssignmentPeer::TABLE_NAME);
            }

        } else { // $values is Assignment object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the assignments table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(AssignmentPeer::TABLE_NAME, $con, AssignmentPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            AssignmentPeer::clearInstancePool();
            AssignmentPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Assignment or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Assignment object or primary key or array of primary keys
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
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            AssignmentPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Assignment) { // it's a model object
            // invalidate the cache for this single object
            AssignmentPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(AssignmentPeer::DATABASE_NAME);
            $criteria->add(AssignmentPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                AssignmentPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(AssignmentPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            AssignmentPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Assignment object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Assignment $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(AssignmentPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(AssignmentPeer::TABLE_NAME);

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

        if ($obj->isNew() || $obj->isColumnModified(AssignmentPeer::TEACHER_ID))
            $columns[AssignmentPeer::TEACHER_ID] = $obj->getTeacherId();

        if ($obj->isNew() || $obj->isColumnModified(AssignmentPeer::COURSE_ID))
            $columns[AssignmentPeer::COURSE_ID] = $obj->getCourseId();

        if ($obj->isNew() || $obj->isColumnModified(AssignmentPeer::ASSIGNMENT_CATEGORY_ID))
            $columns[AssignmentPeer::ASSIGNMENT_CATEGORY_ID] = $obj->getAssignmentCategoryId();

        if ($obj->isNew() || $obj->isColumnModified(AssignmentPeer::NAME))
            $columns[AssignmentPeer::NAME] = $obj->getName();

        if ($obj->isNew() || $obj->isColumnModified(AssignmentPeer::DESCRIPTION))
            $columns[AssignmentPeer::DESCRIPTION] = $obj->getDescription();

        if ($obj->isNew() || $obj->isColumnModified(AssignmentPeer::MAX_POINTS))
            $columns[AssignmentPeer::MAX_POINTS] = $obj->getMaxPoints();

        if ($obj->isNew() || $obj->isColumnModified(AssignmentPeer::DUE_AT))
            $columns[AssignmentPeer::DUE_AT] = $obj->getDueAt();

        }

        return BasePeer::doValidate(AssignmentPeer::DATABASE_NAME, AssignmentPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Assignment
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = AssignmentPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(AssignmentPeer::DATABASE_NAME);
        $criteria->add(AssignmentPeer::ID, $pk);

        $v = AssignmentPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Assignment[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(AssignmentPeer::DATABASE_NAME);
            $criteria->add(AssignmentPeer::ID, $pks, Criteria::IN);
            $objs = AssignmentPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseAssignmentPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseAssignmentPeer::buildTableMap();

EventDispatcherProxy::trigger(array('construct','peer.construct'), new PeerEvent('Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignmentPeer'));
