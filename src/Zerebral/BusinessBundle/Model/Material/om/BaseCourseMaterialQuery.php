<?php

namespace Zerebral\BusinessBundle\Model\Material\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Glorpen\PropelEvent\PropelEventBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\PropelEvent\PropelEventBundle\Events\QueryEvent;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\File\File;
use Zerebral\BusinessBundle\Model\Material\CourseFolder;
use Zerebral\BusinessBundle\Model\Material\CourseMaterial;
use Zerebral\BusinessBundle\Model\Material\CourseMaterialPeer;
use Zerebral\BusinessBundle\Model\Material\CourseMaterialQuery;
use Zerebral\BusinessBundle\Model\User\Teacher;

/**
 * @method CourseMaterialQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CourseMaterialQuery orderByCourseId($order = Criteria::ASC) Order by the course_id column
 * @method CourseMaterialQuery orderByFolderId($order = Criteria::ASC) Order by the folder_id column
 * @method CourseMaterialQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method CourseMaterialQuery orderByFileId($order = Criteria::ASC) Order by the file_id column
 * @method CourseMaterialQuery orderByCreatedBy($order = Criteria::ASC) Order by the created_by column
 * @method CourseMaterialQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 *
 * @method CourseMaterialQuery groupById() Group by the id column
 * @method CourseMaterialQuery groupByCourseId() Group by the course_id column
 * @method CourseMaterialQuery groupByFolderId() Group by the folder_id column
 * @method CourseMaterialQuery groupByDescription() Group by the description column
 * @method CourseMaterialQuery groupByFileId() Group by the file_id column
 * @method CourseMaterialQuery groupByCreatedBy() Group by the created_by column
 * @method CourseMaterialQuery groupByCreatedAt() Group by the created_at column
 *
 * @method CourseMaterialQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CourseMaterialQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CourseMaterialQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CourseMaterialQuery leftJoinTeacher($relationAlias = null) Adds a LEFT JOIN clause to the query using the Teacher relation
 * @method CourseMaterialQuery rightJoinTeacher($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Teacher relation
 * @method CourseMaterialQuery innerJoinTeacher($relationAlias = null) Adds a INNER JOIN clause to the query using the Teacher relation
 *
 * @method CourseMaterialQuery leftJoinCourse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Course relation
 * @method CourseMaterialQuery rightJoinCourse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Course relation
 * @method CourseMaterialQuery innerJoinCourse($relationAlias = null) Adds a INNER JOIN clause to the query using the Course relation
 *
 * @method CourseMaterialQuery leftJoinCourseFolder($relationAlias = null) Adds a LEFT JOIN clause to the query using the CourseFolder relation
 * @method CourseMaterialQuery rightJoinCourseFolder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CourseFolder relation
 * @method CourseMaterialQuery innerJoinCourseFolder($relationAlias = null) Adds a INNER JOIN clause to the query using the CourseFolder relation
 *
 * @method CourseMaterialQuery leftJoinFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the File relation
 * @method CourseMaterialQuery rightJoinFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the File relation
 * @method CourseMaterialQuery innerJoinFile($relationAlias = null) Adds a INNER JOIN clause to the query using the File relation
 *
 * @method CourseMaterial findOne(PropelPDO $con = null) Return the first CourseMaterial matching the query
 * @method CourseMaterial findOneOrCreate(PropelPDO $con = null) Return the first CourseMaterial matching the query, or a new CourseMaterial object populated from the query conditions when no match is found
 *
 * @method CourseMaterial findOneByCourseId(int $course_id) Return the first CourseMaterial filtered by the course_id column
 * @method CourseMaterial findOneByFolderId(int $folder_id) Return the first CourseMaterial filtered by the folder_id column
 * @method CourseMaterial findOneByDescription(string $description) Return the first CourseMaterial filtered by the description column
 * @method CourseMaterial findOneByFileId(int $file_id) Return the first CourseMaterial filtered by the file_id column
 * @method CourseMaterial findOneByCreatedBy(int $created_by) Return the first CourseMaterial filtered by the created_by column
 * @method CourseMaterial findOneByCreatedAt(string $created_at) Return the first CourseMaterial filtered by the created_at column
 *
 * @method array findById(int $id) Return CourseMaterial objects filtered by the id column
 * @method array findByCourseId(int $course_id) Return CourseMaterial objects filtered by the course_id column
 * @method array findByFolderId(int $folder_id) Return CourseMaterial objects filtered by the folder_id column
 * @method array findByDescription(string $description) Return CourseMaterial objects filtered by the description column
 * @method array findByFileId(int $file_id) Return CourseMaterial objects filtered by the file_id column
 * @method array findByCreatedBy(int $created_by) Return CourseMaterial objects filtered by the created_by column
 * @method array findByCreatedAt(string $created_at) Return CourseMaterial objects filtered by the created_at column
 */
abstract class BaseCourseMaterialQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCourseMaterialQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Material\\CourseMaterial', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new CourseMaterialQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     CourseMaterialQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CourseMaterialQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CourseMaterialQuery) {
            return $criteria;
        }
        $query = new CourseMaterialQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   CourseMaterial|CourseMaterial[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CourseMaterialPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CourseMaterialPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return   CourseMaterial A model object, or null if the key is not found
     * @throws   PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return   CourseMaterial A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `course_id`, `folder_id`, `description`, `file_id`, `created_by`, `created_at` FROM `course_materials` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new CourseMaterial();
            $obj->hydrate($row);
            CourseMaterialPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return CourseMaterial|CourseMaterial[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|CourseMaterial[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CourseMaterialPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CourseMaterialPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(CourseMaterialPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the course_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCourseId(1234); // WHERE course_id = 1234
     * $query->filterByCourseId(array(12, 34)); // WHERE course_id IN (12, 34)
     * $query->filterByCourseId(array('min' => 12)); // WHERE course_id > 12
     * </code>
     *
     * @see       filterByCourse()
     *
     * @param     mixed $courseId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterByCourseId($courseId = null, $comparison = null)
    {
        if (is_array($courseId)) {
            $useMinMax = false;
            if (isset($courseId['min'])) {
                $this->addUsingAlias(CourseMaterialPeer::COURSE_ID, $courseId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($courseId['max'])) {
                $this->addUsingAlias(CourseMaterialPeer::COURSE_ID, $courseId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CourseMaterialPeer::COURSE_ID, $courseId, $comparison);
    }

    /**
     * Filter the query on the folder_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFolderId(1234); // WHERE folder_id = 1234
     * $query->filterByFolderId(array(12, 34)); // WHERE folder_id IN (12, 34)
     * $query->filterByFolderId(array('min' => 12)); // WHERE folder_id > 12
     * </code>
     *
     * @see       filterByCourseFolder()
     *
     * @param     mixed $folderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterByFolderId($folderId = null, $comparison = null)
    {
        if (is_array($folderId)) {
            $useMinMax = false;
            if (isset($folderId['min'])) {
                $this->addUsingAlias(CourseMaterialPeer::FOLDER_ID, $folderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($folderId['max'])) {
                $this->addUsingAlias(CourseMaterialPeer::FOLDER_ID, $folderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CourseMaterialPeer::FOLDER_ID, $folderId, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CourseMaterialPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the file_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFileId(1234); // WHERE file_id = 1234
     * $query->filterByFileId(array(12, 34)); // WHERE file_id IN (12, 34)
     * $query->filterByFileId(array('min' => 12)); // WHERE file_id > 12
     * </code>
     *
     * @see       filterByFile()
     *
     * @param     mixed $fileId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterByFileId($fileId = null, $comparison = null)
    {
        if (is_array($fileId)) {
            $useMinMax = false;
            if (isset($fileId['min'])) {
                $this->addUsingAlias(CourseMaterialPeer::FILE_ID, $fileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fileId['max'])) {
                $this->addUsingAlias(CourseMaterialPeer::FILE_ID, $fileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CourseMaterialPeer::FILE_ID, $fileId, $comparison);
    }

    /**
     * Filter the query on the created_by column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedBy(1234); // WHERE created_by = 1234
     * $query->filterByCreatedBy(array(12, 34)); // WHERE created_by IN (12, 34)
     * $query->filterByCreatedBy(array('min' => 12)); // WHERE created_by > 12
     * </code>
     *
     * @see       filterByTeacher()
     *
     * @param     mixed $createdBy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterByCreatedBy($createdBy = null, $comparison = null)
    {
        if (is_array($createdBy)) {
            $useMinMax = false;
            if (isset($createdBy['min'])) {
                $this->addUsingAlias(CourseMaterialPeer::CREATED_BY, $createdBy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdBy['max'])) {
                $this->addUsingAlias(CourseMaterialPeer::CREATED_BY, $createdBy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CourseMaterialPeer::CREATED_BY, $createdBy, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CourseMaterialPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CourseMaterialPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CourseMaterialPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query by a related Teacher object
     *
     * @param   Teacher|PropelObjectCollection $teacher The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseMaterialQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByTeacher($teacher, $comparison = null)
    {
        if ($teacher instanceof Teacher) {
            return $this
                ->addUsingAlias(CourseMaterialPeer::CREATED_BY, $teacher->getId(), $comparison);
        } elseif ($teacher instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CourseMaterialPeer::CREATED_BY, $teacher->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTeacher() only accepts arguments of type Teacher or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Teacher relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function joinTeacher($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Teacher');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Teacher');
        }

        return $this;
    }

    /**
     * Use the Teacher relation Teacher object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\TeacherQuery A secondary query class using the current class as primary query
     */
    public function useTeacherQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTeacher($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Teacher', '\Zerebral\BusinessBundle\Model\User\TeacherQuery');
    }

    /**
     * Filter the query by a related Course object
     *
     * @param   Course|PropelObjectCollection $course The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseMaterialQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourse($course, $comparison = null)
    {
        if ($course instanceof Course) {
            return $this
                ->addUsingAlias(CourseMaterialPeer::COURSE_ID, $course->getId(), $comparison);
        } elseif ($course instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CourseMaterialPeer::COURSE_ID, $course->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCourse() only accepts arguments of type Course or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Course relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function joinCourse($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Course');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Course');
        }

        return $this;
    }

    /**
     * Use the Course relation Course object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Course\CourseQuery A secondary query class using the current class as primary query
     */
    public function useCourseQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCourse($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Course', '\Zerebral\BusinessBundle\Model\Course\CourseQuery');
    }

    /**
     * Filter the query by a related CourseFolder object
     *
     * @param   CourseFolder|PropelObjectCollection $courseFolder The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseMaterialQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourseFolder($courseFolder, $comparison = null)
    {
        if ($courseFolder instanceof CourseFolder) {
            return $this
                ->addUsingAlias(CourseMaterialPeer::FOLDER_ID, $courseFolder->getId(), $comparison);
        } elseif ($courseFolder instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CourseMaterialPeer::FOLDER_ID, $courseFolder->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCourseFolder() only accepts arguments of type CourseFolder or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CourseFolder relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function joinCourseFolder($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CourseFolder');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CourseFolder');
        }

        return $this;
    }

    /**
     * Use the CourseFolder relation CourseFolder object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Material\CourseFolderQuery A secondary query class using the current class as primary query
     */
    public function useCourseFolderQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCourseFolder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CourseFolder', '\Zerebral\BusinessBundle\Model\Material\CourseFolderQuery');
    }

    /**
     * Filter the query by a related File object
     *
     * @param   File|PropelObjectCollection $file The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseMaterialQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFile($file, $comparison = null)
    {
        if ($file instanceof File) {
            return $this
                ->addUsingAlias(CourseMaterialPeer::FILE_ID, $file->getId(), $comparison);
        } elseif ($file instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CourseMaterialPeer::FILE_ID, $file->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByFile() only accepts arguments of type File or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the File relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function joinFile($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('File');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'File');
        }

        return $this;
    }

    /**
     * Use the File relation File object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\File\FileQuery A secondary query class using the current class as primary query
     */
    public function useFileQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'File', '\Zerebral\BusinessBundle\Model\File\FileQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   CourseMaterial $courseMaterial Object to remove from the list of results
     *
     * @return CourseMaterialQuery The current query, for fluid interface
     */
    public function prune($courseMaterial = null)
    {
        if ($courseMaterial) {
            $this->addUsingAlias(CourseMaterialPeer::ID, $courseMaterial->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Code to execute before every SELECT statement
     *
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePreSelect(PropelPDO $con)
    {
        // event behavior
        EventDispatcherProxy::trigger('query.select.pre', new QueryEvent($this));

        return $this->preSelect($con);
    }

    /**
     * Code to execute before every DELETE statement
     *
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePreDelete(PropelPDO $con)
    {
        // event behavior
        EventDispatcherProxy::trigger(array('delete.pre','query.delete.pre'), new QueryEvent($this));

        return $this->preDelete($con);
    }

    /**
     * Code to execute after every DELETE statement
     *
     * @param     int $affectedRows the number of deleted rows
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePostDelete($affectedRows, PropelPDO $con)
    {
        // event behavior
        EventDispatcherProxy::trigger(array('delete.post','query.delete.post'), new QueryEvent($this));

        return $this->postDelete($affectedRows, $con);
    }

    /**
     * Code to execute before every UPDATE statement
     *
     * @param     array $values The associatiove array of columns and values for the update
     * @param     PropelPDO $con The connection object used by the query
     * @param     boolean $forceIndividualSaves If false (default), the resulting call is a BasePeer::doUpdate(), ortherwise it is a series of save() calls on all the found objects
     */
    protected function basePreUpdate(&$values, PropelPDO $con, $forceIndividualSaves = false)
    {
        // event behavior
        EventDispatcherProxy::trigger(array('update.pre', 'query.update.pre'), new QueryEvent($this));

        return $this->preUpdate($values, $con, $forceIndividualSaves);
    }

    /**
     * Code to execute after every UPDATE statement
     *
     * @param     int $affectedRows the number of udated rows
     * @param     PropelPDO $con The connection object used by the query
     */
    protected function basePostUpdate($affectedRows, PropelPDO $con)
    {
        // event behavior
        EventDispatcherProxy::trigger(array('update.post', 'query.update.post'), new QueryEvent($this));

        return $this->postUpdate($affectedRows, $con);
    }

}
