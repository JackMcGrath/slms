<?php

namespace Zerebral\BusinessBundle\Model\Course\om;

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
use Zerebral\BusinessBundle\Model\Assignment\Assignment;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CoursePeer;
use Zerebral\BusinessBundle\Model\Course\CourseQuery;
use Zerebral\BusinessBundle\Model\Course\CourseScheduleDay;
use Zerebral\BusinessBundle\Model\Course\CourseStudent;
use Zerebral\BusinessBundle\Model\Course\CourseTeacher;
use Zerebral\BusinessBundle\Model\Course\Discipline;
use Zerebral\BusinessBundle\Model\Course\GradeLevel;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\Teacher;

/**
 * @method CourseQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CourseQuery orderByDisciplineId($order = Criteria::ASC) Order by the discipline_id column
 * @method CourseQuery orderByGradeLevelId($order = Criteria::ASC) Order by the grade_level_id column
 * @method CourseQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method CourseQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method CourseQuery orderByAccessCode($order = Criteria::ASC) Order by the access_code column
 * @method CourseQuery orderByCreatedBy($order = Criteria::ASC) Order by the created_by column
 * @method CourseQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method CourseQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method CourseQuery groupById() Group by the id column
 * @method CourseQuery groupByDisciplineId() Group by the discipline_id column
 * @method CourseQuery groupByGradeLevelId() Group by the grade_level_id column
 * @method CourseQuery groupByName() Group by the name column
 * @method CourseQuery groupByDescription() Group by the description column
 * @method CourseQuery groupByAccessCode() Group by the access_code column
 * @method CourseQuery groupByCreatedBy() Group by the created_by column
 * @method CourseQuery groupByCreatedAt() Group by the created_at column
 * @method CourseQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method CourseQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CourseQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CourseQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CourseQuery leftJoinCreatedByTeacher($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreatedByTeacher relation
 * @method CourseQuery rightJoinCreatedByTeacher($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreatedByTeacher relation
 * @method CourseQuery innerJoinCreatedByTeacher($relationAlias = null) Adds a INNER JOIN clause to the query using the CreatedByTeacher relation
 *
 * @method CourseQuery leftJoinDiscipline($relationAlias = null) Adds a LEFT JOIN clause to the query using the Discipline relation
 * @method CourseQuery rightJoinDiscipline($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Discipline relation
 * @method CourseQuery innerJoinDiscipline($relationAlias = null) Adds a INNER JOIN clause to the query using the Discipline relation
 *
 * @method CourseQuery leftJoinGradeLevel($relationAlias = null) Adds a LEFT JOIN clause to the query using the GradeLevel relation
 * @method CourseQuery rightJoinGradeLevel($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GradeLevel relation
 * @method CourseQuery innerJoinGradeLevel($relationAlias = null) Adds a INNER JOIN clause to the query using the GradeLevel relation
 *
 * @method CourseQuery leftJoinAssignmentCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the AssignmentCategory relation
 * @method CourseQuery rightJoinAssignmentCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AssignmentCategory relation
 * @method CourseQuery innerJoinAssignmentCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the AssignmentCategory relation
 *
 * @method CourseQuery leftJoinAssignment($relationAlias = null) Adds a LEFT JOIN clause to the query using the Assignment relation
 * @method CourseQuery rightJoinAssignment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Assignment relation
 * @method CourseQuery innerJoinAssignment($relationAlias = null) Adds a INNER JOIN clause to the query using the Assignment relation
 *
 * @method CourseQuery leftJoinCourseStudent($relationAlias = null) Adds a LEFT JOIN clause to the query using the CourseStudent relation
 * @method CourseQuery rightJoinCourseStudent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CourseStudent relation
 * @method CourseQuery innerJoinCourseStudent($relationAlias = null) Adds a INNER JOIN clause to the query using the CourseStudent relation
 *
 * @method CourseQuery leftJoinCourseTeacher($relationAlias = null) Adds a LEFT JOIN clause to the query using the CourseTeacher relation
 * @method CourseQuery rightJoinCourseTeacher($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CourseTeacher relation
 * @method CourseQuery innerJoinCourseTeacher($relationAlias = null) Adds a INNER JOIN clause to the query using the CourseTeacher relation
 *
 * @method CourseQuery leftJoinCourseScheduleDay($relationAlias = null) Adds a LEFT JOIN clause to the query using the CourseScheduleDay relation
 * @method CourseQuery rightJoinCourseScheduleDay($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CourseScheduleDay relation
 * @method CourseQuery innerJoinCourseScheduleDay($relationAlias = null) Adds a INNER JOIN clause to the query using the CourseScheduleDay relation
 *
 * @method Course findOne(PropelPDO $con = null) Return the first Course matching the query
 * @method Course findOneOrCreate(PropelPDO $con = null) Return the first Course matching the query, or a new Course object populated from the query conditions when no match is found
 *
 * @method Course findOneByDisciplineId(int $discipline_id) Return the first Course filtered by the discipline_id column
 * @method Course findOneByGradeLevelId(int $grade_level_id) Return the first Course filtered by the grade_level_id column
 * @method Course findOneByName(string $name) Return the first Course filtered by the name column
 * @method Course findOneByDescription(string $description) Return the first Course filtered by the description column
 * @method Course findOneByAccessCode(string $access_code) Return the first Course filtered by the access_code column
 * @method Course findOneByCreatedBy(int $created_by) Return the first Course filtered by the created_by column
 * @method Course findOneByCreatedAt(string $created_at) Return the first Course filtered by the created_at column
 * @method Course findOneByUpdatedAt(string $updated_at) Return the first Course filtered by the updated_at column
 *
 * @method array findById(int $id) Return Course objects filtered by the id column
 * @method array findByDisciplineId(int $discipline_id) Return Course objects filtered by the discipline_id column
 * @method array findByGradeLevelId(int $grade_level_id) Return Course objects filtered by the grade_level_id column
 * @method array findByName(string $name) Return Course objects filtered by the name column
 * @method array findByDescription(string $description) Return Course objects filtered by the description column
 * @method array findByAccessCode(string $access_code) Return Course objects filtered by the access_code column
 * @method array findByCreatedBy(int $created_by) Return Course objects filtered by the created_by column
 * @method array findByCreatedAt(string $created_at) Return Course objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Course objects filtered by the updated_at column
 */
abstract class BaseCourseQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCourseQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Course\\Course', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new CourseQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     CourseQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CourseQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CourseQuery) {
            return $criteria;
        }
        $query = new CourseQuery();
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
     * @return   Course|Course[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CoursePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CoursePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Course A model object, or null if the key is not found
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
     * @return   Course A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `discipline_id`, `grade_level_id`, `name`, `description`, `access_code`, `created_by`, `created_at`, `updated_at` FROM `courses` WHERE `id` = :p0';
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
            $obj = new Course();
            $obj->hydrate($row);
            CoursePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Course|Course[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Course[]|mixed the list of results, formatted by the current formatter
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
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CoursePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CoursePeer::ID, $keys, Criteria::IN);
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
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(CoursePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the discipline_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDisciplineId(1234); // WHERE discipline_id = 1234
     * $query->filterByDisciplineId(array(12, 34)); // WHERE discipline_id IN (12, 34)
     * $query->filterByDisciplineId(array('min' => 12)); // WHERE discipline_id > 12
     * </code>
     *
     * @see       filterByDiscipline()
     *
     * @param     mixed $disciplineId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByDisciplineId($disciplineId = null, $comparison = null)
    {
        if (is_array($disciplineId)) {
            $useMinMax = false;
            if (isset($disciplineId['min'])) {
                $this->addUsingAlias(CoursePeer::DISCIPLINE_ID, $disciplineId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($disciplineId['max'])) {
                $this->addUsingAlias(CoursePeer::DISCIPLINE_ID, $disciplineId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CoursePeer::DISCIPLINE_ID, $disciplineId, $comparison);
    }

    /**
     * Filter the query on the grade_level_id column
     *
     * Example usage:
     * <code>
     * $query->filterByGradeLevelId(1234); // WHERE grade_level_id = 1234
     * $query->filterByGradeLevelId(array(12, 34)); // WHERE grade_level_id IN (12, 34)
     * $query->filterByGradeLevelId(array('min' => 12)); // WHERE grade_level_id > 12
     * </code>
     *
     * @see       filterByGradeLevel()
     *
     * @param     mixed $gradeLevelId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByGradeLevelId($gradeLevelId = null, $comparison = null)
    {
        if (is_array($gradeLevelId)) {
            $useMinMax = false;
            if (isset($gradeLevelId['min'])) {
                $this->addUsingAlias(CoursePeer::GRADE_LEVEL_ID, $gradeLevelId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($gradeLevelId['max'])) {
                $this->addUsingAlias(CoursePeer::GRADE_LEVEL_ID, $gradeLevelId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CoursePeer::GRADE_LEVEL_ID, $gradeLevelId, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CoursePeer::NAME, $name, $comparison);
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
     * @return CourseQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CoursePeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the access_code column
     *
     * Example usage:
     * <code>
     * $query->filterByAccessCode('fooValue');   // WHERE access_code = 'fooValue'
     * $query->filterByAccessCode('%fooValue%'); // WHERE access_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $accessCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByAccessCode($accessCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($accessCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $accessCode)) {
                $accessCode = str_replace('*', '%', $accessCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CoursePeer::ACCESS_CODE, $accessCode, $comparison);
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
     * @see       filterByCreatedByTeacher()
     *
     * @param     mixed $createdBy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByCreatedBy($createdBy = null, $comparison = null)
    {
        if (is_array($createdBy)) {
            $useMinMax = false;
            if (isset($createdBy['min'])) {
                $this->addUsingAlias(CoursePeer::CREATED_BY, $createdBy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdBy['max'])) {
                $this->addUsingAlias(CoursePeer::CREATED_BY, $createdBy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CoursePeer::CREATED_BY, $createdBy, $comparison);
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
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CoursePeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CoursePeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CoursePeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CoursePeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CoursePeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CoursePeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related Teacher object
     *
     * @param   Teacher|PropelObjectCollection $teacher The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCreatedByTeacher($teacher, $comparison = null)
    {
        if ($teacher instanceof Teacher) {
            return $this
                ->addUsingAlias(CoursePeer::CREATED_BY, $teacher->getId(), $comparison);
        } elseif ($teacher instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CoursePeer::CREATED_BY, $teacher->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCreatedByTeacher() only accepts arguments of type Teacher or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CreatedByTeacher relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function joinCreatedByTeacher($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CreatedByTeacher');

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
            $this->addJoinObject($join, 'CreatedByTeacher');
        }

        return $this;
    }

    /**
     * Use the CreatedByTeacher relation Teacher object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\TeacherQuery A secondary query class using the current class as primary query
     */
    public function useCreatedByTeacherQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCreatedByTeacher($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreatedByTeacher', '\Zerebral\BusinessBundle\Model\User\TeacherQuery');
    }

    /**
     * Filter the query by a related Discipline object
     *
     * @param   Discipline|PropelObjectCollection $discipline The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByDiscipline($discipline, $comparison = null)
    {
        if ($discipline instanceof Discipline) {
            return $this
                ->addUsingAlias(CoursePeer::DISCIPLINE_ID, $discipline->getId(), $comparison);
        } elseif ($discipline instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CoursePeer::DISCIPLINE_ID, $discipline->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDiscipline() only accepts arguments of type Discipline or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Discipline relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function joinDiscipline($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Discipline');

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
            $this->addJoinObject($join, 'Discipline');
        }

        return $this;
    }

    /**
     * Use the Discipline relation Discipline object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Course\DisciplineQuery A secondary query class using the current class as primary query
     */
    public function useDisciplineQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDiscipline($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Discipline', '\Zerebral\BusinessBundle\Model\Course\DisciplineQuery');
    }

    /**
     * Filter the query by a related GradeLevel object
     *
     * @param   GradeLevel|PropelObjectCollection $gradeLevel The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByGradeLevel($gradeLevel, $comparison = null)
    {
        if ($gradeLevel instanceof GradeLevel) {
            return $this
                ->addUsingAlias(CoursePeer::GRADE_LEVEL_ID, $gradeLevel->getId(), $comparison);
        } elseif ($gradeLevel instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CoursePeer::GRADE_LEVEL_ID, $gradeLevel->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByGradeLevel() only accepts arguments of type GradeLevel or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the GradeLevel relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function joinGradeLevel($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GradeLevel');

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
            $this->addJoinObject($join, 'GradeLevel');
        }

        return $this;
    }

    /**
     * Use the GradeLevel relation GradeLevel object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Course\GradeLevelQuery A secondary query class using the current class as primary query
     */
    public function useGradeLevelQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinGradeLevel($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GradeLevel', '\Zerebral\BusinessBundle\Model\Course\GradeLevelQuery');
    }

    /**
     * Filter the query by a related AssignmentCategory object
     *
     * @param   AssignmentCategory|PropelObjectCollection $assignmentCategory  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByAssignmentCategory($assignmentCategory, $comparison = null)
    {
        if ($assignmentCategory instanceof AssignmentCategory) {
            return $this
                ->addUsingAlias(CoursePeer::ID, $assignmentCategory->getCourseId(), $comparison);
        } elseif ($assignmentCategory instanceof PropelObjectCollection) {
            return $this
                ->useAssignmentCategoryQuery()
                ->filterByPrimaryKeys($assignmentCategory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAssignmentCategory() only accepts arguments of type AssignmentCategory or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AssignmentCategory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function joinAssignmentCategory($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AssignmentCategory');

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
            $this->addJoinObject($join, 'AssignmentCategory');
        }

        return $this;
    }

    /**
     * Use the AssignmentCategory relation AssignmentCategory object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery A secondary query class using the current class as primary query
     */
    public function useAssignmentCategoryQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAssignmentCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AssignmentCategory', '\Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery');
    }

    /**
     * Filter the query by a related Assignment object
     *
     * @param   Assignment|PropelObjectCollection $assignment  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByAssignment($assignment, $comparison = null)
    {
        if ($assignment instanceof Assignment) {
            return $this
                ->addUsingAlias(CoursePeer::ID, $assignment->getCourseId(), $comparison);
        } elseif ($assignment instanceof PropelObjectCollection) {
            return $this
                ->useAssignmentQuery()
                ->filterByPrimaryKeys($assignment->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAssignment() only accepts arguments of type Assignment or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Assignment relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function joinAssignment($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Assignment');

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
            $this->addJoinObject($join, 'Assignment');
        }

        return $this;
    }

    /**
     * Use the Assignment relation Assignment object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery A secondary query class using the current class as primary query
     */
    public function useAssignmentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAssignment($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Assignment', '\Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery');
    }

    /**
     * Filter the query by a related CourseStudent object
     *
     * @param   CourseStudent|PropelObjectCollection $courseStudent  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourseStudent($courseStudent, $comparison = null)
    {
        if ($courseStudent instanceof CourseStudent) {
            return $this
                ->addUsingAlias(CoursePeer::ID, $courseStudent->getCourseId(), $comparison);
        } elseif ($courseStudent instanceof PropelObjectCollection) {
            return $this
                ->useCourseStudentQuery()
                ->filterByPrimaryKeys($courseStudent->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCourseStudent() only accepts arguments of type CourseStudent or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CourseStudent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function joinCourseStudent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CourseStudent');

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
            $this->addJoinObject($join, 'CourseStudent');
        }

        return $this;
    }

    /**
     * Use the CourseStudent relation CourseStudent object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Course\CourseStudentQuery A secondary query class using the current class as primary query
     */
    public function useCourseStudentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCourseStudent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CourseStudent', '\Zerebral\BusinessBundle\Model\Course\CourseStudentQuery');
    }

    /**
     * Filter the query by a related CourseTeacher object
     *
     * @param   CourseTeacher|PropelObjectCollection $courseTeacher  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourseTeacher($courseTeacher, $comparison = null)
    {
        if ($courseTeacher instanceof CourseTeacher) {
            return $this
                ->addUsingAlias(CoursePeer::ID, $courseTeacher->getCourseId(), $comparison);
        } elseif ($courseTeacher instanceof PropelObjectCollection) {
            return $this
                ->useCourseTeacherQuery()
                ->filterByPrimaryKeys($courseTeacher->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCourseTeacher() only accepts arguments of type CourseTeacher or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CourseTeacher relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function joinCourseTeacher($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CourseTeacher');

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
            $this->addJoinObject($join, 'CourseTeacher');
        }

        return $this;
    }

    /**
     * Use the CourseTeacher relation CourseTeacher object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Course\CourseTeacherQuery A secondary query class using the current class as primary query
     */
    public function useCourseTeacherQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCourseTeacher($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CourseTeacher', '\Zerebral\BusinessBundle\Model\Course\CourseTeacherQuery');
    }

    /**
     * Filter the query by a related CourseScheduleDay object
     *
     * @param   CourseScheduleDay|PropelObjectCollection $courseScheduleDay  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourseScheduleDay($courseScheduleDay, $comparison = null)
    {
        if ($courseScheduleDay instanceof CourseScheduleDay) {
            return $this
                ->addUsingAlias(CoursePeer::ID, $courseScheduleDay->getCourseId(), $comparison);
        } elseif ($courseScheduleDay instanceof PropelObjectCollection) {
            return $this
                ->useCourseScheduleDayQuery()
                ->filterByPrimaryKeys($courseScheduleDay->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCourseScheduleDay() only accepts arguments of type CourseScheduleDay or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CourseScheduleDay relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function joinCourseScheduleDay($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CourseScheduleDay');

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
            $this->addJoinObject($join, 'CourseScheduleDay');
        }

        return $this;
    }

    /**
     * Use the CourseScheduleDay relation CourseScheduleDay object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Course\CourseScheduleDayQuery A secondary query class using the current class as primary query
     */
    public function useCourseScheduleDayQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCourseScheduleDay($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CourseScheduleDay', '\Zerebral\BusinessBundle\Model\Course\CourseScheduleDayQuery');
    }

    /**
     * Filter the query by a related Student object
     * using the course_students table as cross reference
     *
     * @param   Student $student the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     */
    public function filterByStudent($student, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useCourseStudentQuery()
            ->filterByStudent($student, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Teacher object
     * using the course_teachers table as cross reference
     *
     * @param   Teacher $teacher the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseQuery The current query, for fluid interface
     */
    public function filterByTeacher($teacher, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useCourseTeacherQuery()
            ->filterByTeacher($teacher, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Course $course Object to remove from the list of results
     *
     * @return CourseQuery The current query, for fluid interface
     */
    public function prune($course = null)
    {
        if ($course) {
            $this->addUsingAlias(CoursePeer::ID, $course->getId(), Criteria::NOT_EQUAL);
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
