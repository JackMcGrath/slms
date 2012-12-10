<?php

namespace Zerebral\BusinessBundle\Model\Assignment\om;

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
use Zerebral\BusinessBundle\Model\Assignment\AssignmentPeer;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignment;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\Teacher;

/**
 * @method AssignmentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method AssignmentQuery orderByTeacherId($order = Criteria::ASC) Order by the teacher_id column
 * @method AssignmentQuery orderByCourseId($order = Criteria::ASC) Order by the course_id column
 * @method AssignmentQuery orderByAssignmentCategoryId($order = Criteria::ASC) Order by the assignment_category_id column
 * @method AssignmentQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method AssignmentQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method AssignmentQuery orderByMaxPoints($order = Criteria::ASC) Order by the max_points column
 * @method AssignmentQuery orderByDueAt($order = Criteria::ASC) Order by the due_at column
 *
 * @method AssignmentQuery groupById() Group by the id column
 * @method AssignmentQuery groupByTeacherId() Group by the teacher_id column
 * @method AssignmentQuery groupByCourseId() Group by the course_id column
 * @method AssignmentQuery groupByAssignmentCategoryId() Group by the assignment_category_id column
 * @method AssignmentQuery groupByName() Group by the name column
 * @method AssignmentQuery groupByDescription() Group by the description column
 * @method AssignmentQuery groupByMaxPoints() Group by the max_points column
 * @method AssignmentQuery groupByDueAt() Group by the due_at column
 *
 * @method AssignmentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method AssignmentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method AssignmentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method AssignmentQuery leftJoinTeacher($relationAlias = null) Adds a LEFT JOIN clause to the query using the Teacher relation
 * @method AssignmentQuery rightJoinTeacher($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Teacher relation
 * @method AssignmentQuery innerJoinTeacher($relationAlias = null) Adds a INNER JOIN clause to the query using the Teacher relation
 *
 * @method AssignmentQuery leftJoinCourse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Course relation
 * @method AssignmentQuery rightJoinCourse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Course relation
 * @method AssignmentQuery innerJoinCourse($relationAlias = null) Adds a INNER JOIN clause to the query using the Course relation
 *
 * @method AssignmentQuery leftJoinAssignmentCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the AssignmentCategory relation
 * @method AssignmentQuery rightJoinAssignmentCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AssignmentCategory relation
 * @method AssignmentQuery innerJoinAssignmentCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the AssignmentCategory relation
 *
 * @method AssignmentQuery leftJoinStudentAssignment($relationAlias = null) Adds a LEFT JOIN clause to the query using the StudentAssignment relation
 * @method AssignmentQuery rightJoinStudentAssignment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the StudentAssignment relation
 * @method AssignmentQuery innerJoinStudentAssignment($relationAlias = null) Adds a INNER JOIN clause to the query using the StudentAssignment relation
 *
 * @method Assignment findOne(PropelPDO $con = null) Return the first Assignment matching the query
 * @method Assignment findOneOrCreate(PropelPDO $con = null) Return the first Assignment matching the query, or a new Assignment object populated from the query conditions when no match is found
 *
 * @method Assignment findOneByTeacherId(int $teacher_id) Return the first Assignment filtered by the teacher_id column
 * @method Assignment findOneByCourseId(int $course_id) Return the first Assignment filtered by the course_id column
 * @method Assignment findOneByAssignmentCategoryId(int $assignment_category_id) Return the first Assignment filtered by the assignment_category_id column
 * @method Assignment findOneByName(string $name) Return the first Assignment filtered by the name column
 * @method Assignment findOneByDescription(string $description) Return the first Assignment filtered by the description column
 * @method Assignment findOneByMaxPoints(int $max_points) Return the first Assignment filtered by the max_points column
 * @method Assignment findOneByDueAt(string $due_at) Return the first Assignment filtered by the due_at column
 *
 * @method array findById(int $id) Return Assignment objects filtered by the id column
 * @method array findByTeacherId(int $teacher_id) Return Assignment objects filtered by the teacher_id column
 * @method array findByCourseId(int $course_id) Return Assignment objects filtered by the course_id column
 * @method array findByAssignmentCategoryId(int $assignment_category_id) Return Assignment objects filtered by the assignment_category_id column
 * @method array findByName(string $name) Return Assignment objects filtered by the name column
 * @method array findByDescription(string $description) Return Assignment objects filtered by the description column
 * @method array findByMaxPoints(int $max_points) Return Assignment objects filtered by the max_points column
 * @method array findByDueAt(string $due_at) Return Assignment objects filtered by the due_at column
 */
abstract class BaseAssignmentQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseAssignmentQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new AssignmentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     AssignmentQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AssignmentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AssignmentQuery) {
            return $criteria;
        }
        $query = new AssignmentQuery();
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
     * @return   Assignment|Assignment[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AssignmentPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Assignment A model object, or null if the key is not found
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
     * @return   Assignment A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `teacher_id`, `course_id`, `assignment_category_id`, `name`, `description`, `max_points`, `due_at` FROM `assignments` WHERE `id` = :p0';
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
            $obj = new Assignment();
            $obj->hydrate($row);
            AssignmentPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Assignment|Assignment[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Assignment[]|mixed the list of results, formatted by the current formatter
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
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AssignmentPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AssignmentPeer::ID, $keys, Criteria::IN);
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
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(AssignmentPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the teacher_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTeacherId(1234); // WHERE teacher_id = 1234
     * $query->filterByTeacherId(array(12, 34)); // WHERE teacher_id IN (12, 34)
     * $query->filterByTeacherId(array('min' => 12)); // WHERE teacher_id > 12
     * </code>
     *
     * @see       filterByTeacher()
     *
     * @param     mixed $teacherId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function filterByTeacherId($teacherId = null, $comparison = null)
    {
        if (is_array($teacherId)) {
            $useMinMax = false;
            if (isset($teacherId['min'])) {
                $this->addUsingAlias(AssignmentPeer::TEACHER_ID, $teacherId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($teacherId['max'])) {
                $this->addUsingAlias(AssignmentPeer::TEACHER_ID, $teacherId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AssignmentPeer::TEACHER_ID, $teacherId, $comparison);
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
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function filterByCourseId($courseId = null, $comparison = null)
    {
        if (is_array($courseId)) {
            $useMinMax = false;
            if (isset($courseId['min'])) {
                $this->addUsingAlias(AssignmentPeer::COURSE_ID, $courseId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($courseId['max'])) {
                $this->addUsingAlias(AssignmentPeer::COURSE_ID, $courseId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AssignmentPeer::COURSE_ID, $courseId, $comparison);
    }

    /**
     * Filter the query on the assignment_category_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAssignmentCategoryId(1234); // WHERE assignment_category_id = 1234
     * $query->filterByAssignmentCategoryId(array(12, 34)); // WHERE assignment_category_id IN (12, 34)
     * $query->filterByAssignmentCategoryId(array('min' => 12)); // WHERE assignment_category_id > 12
     * </code>
     *
     * @see       filterByAssignmentCategory()
     *
     * @param     mixed $assignmentCategoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function filterByAssignmentCategoryId($assignmentCategoryId = null, $comparison = null)
    {
        if (is_array($assignmentCategoryId)) {
            $useMinMax = false;
            if (isset($assignmentCategoryId['min'])) {
                $this->addUsingAlias(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, $assignmentCategoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($assignmentCategoryId['max'])) {
                $this->addUsingAlias(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, $assignmentCategoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, $assignmentCategoryId, $comparison);
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
     * @return AssignmentQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AssignmentPeer::NAME, $name, $comparison);
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
     * @return AssignmentQuery The current query, for fluid interface
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

        return $this->addUsingAlias(AssignmentPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the max_points column
     *
     * Example usage:
     * <code>
     * $query->filterByMaxPoints(1234); // WHERE max_points = 1234
     * $query->filterByMaxPoints(array(12, 34)); // WHERE max_points IN (12, 34)
     * $query->filterByMaxPoints(array('min' => 12)); // WHERE max_points > 12
     * </code>
     *
     * @param     mixed $maxPoints The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function filterByMaxPoints($maxPoints = null, $comparison = null)
    {
        if (is_array($maxPoints)) {
            $useMinMax = false;
            if (isset($maxPoints['min'])) {
                $this->addUsingAlias(AssignmentPeer::MAX_POINTS, $maxPoints['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($maxPoints['max'])) {
                $this->addUsingAlias(AssignmentPeer::MAX_POINTS, $maxPoints['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AssignmentPeer::MAX_POINTS, $maxPoints, $comparison);
    }

    /**
     * Filter the query on the due_at column
     *
     * Example usage:
     * <code>
     * $query->filterByDueAt('2011-03-14'); // WHERE due_at = '2011-03-14'
     * $query->filterByDueAt('now'); // WHERE due_at = '2011-03-14'
     * $query->filterByDueAt(array('max' => 'yesterday')); // WHERE due_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $dueAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function filterByDueAt($dueAt = null, $comparison = null)
    {
        if (is_array($dueAt)) {
            $useMinMax = false;
            if (isset($dueAt['min'])) {
                $this->addUsingAlias(AssignmentPeer::DUE_AT, $dueAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dueAt['max'])) {
                $this->addUsingAlias(AssignmentPeer::DUE_AT, $dueAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AssignmentPeer::DUE_AT, $dueAt, $comparison);
    }

    /**
     * Filter the query by a related Teacher object
     *
     * @param   Teacher|PropelObjectCollection $teacher The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   AssignmentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByTeacher($teacher, $comparison = null)
    {
        if ($teacher instanceof Teacher) {
            return $this
                ->addUsingAlias(AssignmentPeer::TEACHER_ID, $teacher->getId(), $comparison);
        } elseif ($teacher instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AssignmentPeer::TEACHER_ID, $teacher->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return AssignmentQuery The current query, for fluid interface
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
     * @return   AssignmentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourse($course, $comparison = null)
    {
        if ($course instanceof Course) {
            return $this
                ->addUsingAlias(AssignmentPeer::COURSE_ID, $course->getId(), $comparison);
        } elseif ($course instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AssignmentPeer::COURSE_ID, $course->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return AssignmentQuery The current query, for fluid interface
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
     * Filter the query by a related AssignmentCategory object
     *
     * @param   AssignmentCategory|PropelObjectCollection $assignmentCategory The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   AssignmentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByAssignmentCategory($assignmentCategory, $comparison = null)
    {
        if ($assignmentCategory instanceof AssignmentCategory) {
            return $this
                ->addUsingAlias(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, $assignmentCategory->getId(), $comparison);
        } elseif ($assignmentCategory instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(AssignmentPeer::ASSIGNMENT_CATEGORY_ID, $assignmentCategory->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function joinAssignmentCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useAssignmentCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAssignmentCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AssignmentCategory', '\Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery');
    }

    /**
     * Filter the query by a related StudentAssignment object
     *
     * @param   StudentAssignment|PropelObjectCollection $studentAssignment  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   AssignmentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByStudentAssignment($studentAssignment, $comparison = null)
    {
        if ($studentAssignment instanceof StudentAssignment) {
            return $this
                ->addUsingAlias(AssignmentPeer::ID, $studentAssignment->getAssignmentId(), $comparison);
        } elseif ($studentAssignment instanceof PropelObjectCollection) {
            return $this
                ->useStudentAssignmentQuery()
                ->filterByPrimaryKeys($studentAssignment->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByStudentAssignment() only accepts arguments of type StudentAssignment or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the StudentAssignment relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function joinStudentAssignment($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('StudentAssignment');

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
            $this->addJoinObject($join, 'StudentAssignment');
        }

        return $this;
    }

    /**
     * Use the StudentAssignment relation StudentAssignment object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery A secondary query class using the current class as primary query
     */
    public function useStudentAssignmentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinStudentAssignment($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'StudentAssignment', '\Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery');
    }

    /**
     * Filter the query by a related Student object
     * using the student_assignments table as cross reference
     *
     * @param   Student $student the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   AssignmentQuery The current query, for fluid interface
     */
    public function filterByStudent($student, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useStudentAssignmentQuery()
            ->filterByStudent($student, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Assignment $assignment Object to remove from the list of results
     *
     * @return AssignmentQuery The current query, for fluid interface
     */
    public function prune($assignment = null)
    {
        if ($assignment) {
            $this->addUsingAlias(AssignmentPeer::ID, $assignment->getId(), Criteria::NOT_EQUAL);
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
