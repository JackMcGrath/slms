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
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignment;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;
use Zerebral\BusinessBundle\Model\File\File;
use Zerebral\BusinessBundle\Model\File\FileReferences;
use Zerebral\BusinessBundle\Model\Message\Message;
use Zerebral\BusinessBundle\Model\User\Student;

/**
 * @method StudentAssignmentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method StudentAssignmentQuery orderByStudentId($order = Criteria::ASC) Order by the student_id column
 * @method StudentAssignmentQuery orderByAssignmentId($order = Criteria::ASC) Order by the assignment_id column
 * @method StudentAssignmentQuery orderByIsSubmitted($order = Criteria::ASC) Order by the is_submitted column
 * @method StudentAssignmentQuery orderByGrading($order = Criteria::ASC) Order by the grading column
 * @method StudentAssignmentQuery orderByGradingComment($order = Criteria::ASC) Order by the grading_comment column
 * @method StudentAssignmentQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 *
 * @method StudentAssignmentQuery groupById() Group by the id column
 * @method StudentAssignmentQuery groupByStudentId() Group by the student_id column
 * @method StudentAssignmentQuery groupByAssignmentId() Group by the assignment_id column
 * @method StudentAssignmentQuery groupByIsSubmitted() Group by the is_submitted column
 * @method StudentAssignmentQuery groupByGrading() Group by the grading column
 * @method StudentAssignmentQuery groupByGradingComment() Group by the grading_comment column
 * @method StudentAssignmentQuery groupByCreatedAt() Group by the created_at column
 *
 * @method StudentAssignmentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method StudentAssignmentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method StudentAssignmentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method StudentAssignmentQuery leftJoinStudent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Student relation
 * @method StudentAssignmentQuery rightJoinStudent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Student relation
 * @method StudentAssignmentQuery innerJoinStudent($relationAlias = null) Adds a INNER JOIN clause to the query using the Student relation
 *
 * @method StudentAssignmentQuery leftJoinAssignment($relationAlias = null) Adds a LEFT JOIN clause to the query using the Assignment relation
 * @method StudentAssignmentQuery rightJoinAssignment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Assignment relation
 * @method StudentAssignmentQuery innerJoinAssignment($relationAlias = null) Adds a INNER JOIN clause to the query using the Assignment relation
 *
 * @method StudentAssignmentQuery leftJoinFileReferences($relationAlias = null) Adds a LEFT JOIN clause to the query using the FileReferences relation
 * @method StudentAssignmentQuery rightJoinFileReferences($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FileReferences relation
 * @method StudentAssignmentQuery innerJoinFileReferences($relationAlias = null) Adds a INNER JOIN clause to the query using the FileReferences relation
 *
 * @method StudentAssignment findOne(PropelPDO $con = null) Return the first StudentAssignment matching the query
 * @method StudentAssignment findOneOrCreate(PropelPDO $con = null) Return the first StudentAssignment matching the query, or a new StudentAssignment object populated from the query conditions when no match is found
 *
 * @method StudentAssignment findOneByStudentId(int $student_id) Return the first StudentAssignment filtered by the student_id column
 * @method StudentAssignment findOneByAssignmentId(int $assignment_id) Return the first StudentAssignment filtered by the assignment_id column
 * @method StudentAssignment findOneByIsSubmitted(boolean $is_submitted) Return the first StudentAssignment filtered by the is_submitted column
 * @method StudentAssignment findOneByGrading(string $grading) Return the first StudentAssignment filtered by the grading column
 * @method StudentAssignment findOneByGradingComment(string $grading_comment) Return the first StudentAssignment filtered by the grading_comment column
 * @method StudentAssignment findOneByCreatedAt(string $created_at) Return the first StudentAssignment filtered by the created_at column
 *
 * @method array findById(int $id) Return StudentAssignment objects filtered by the id column
 * @method array findByStudentId(int $student_id) Return StudentAssignment objects filtered by the student_id column
 * @method array findByAssignmentId(int $assignment_id) Return StudentAssignment objects filtered by the assignment_id column
 * @method array findByIsSubmitted(boolean $is_submitted) Return StudentAssignment objects filtered by the is_submitted column
 * @method array findByGrading(string $grading) Return StudentAssignment objects filtered by the grading column
 * @method array findByGradingComment(string $grading_comment) Return StudentAssignment objects filtered by the grading_comment column
 * @method array findByCreatedAt(string $created_at) Return StudentAssignment objects filtered by the created_at column
 */
abstract class BaseStudentAssignmentQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseStudentAssignmentQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignment', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new StudentAssignmentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     StudentAssignmentQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return StudentAssignmentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof StudentAssignmentQuery) {
            return $criteria;
        }
        $query = new StudentAssignmentQuery();
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
     * @return   StudentAssignment|StudentAssignment[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = StudentAssignmentPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(StudentAssignmentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   StudentAssignment A model object, or null if the key is not found
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
     * @return   StudentAssignment A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `student_id`, `assignment_id`, `is_submitted`, `grading`, `grading_comment`, `created_at` FROM `student_assignments` WHERE `id` = :p0';
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
            $obj = new StudentAssignment();
            $obj->hydrate($row);
            StudentAssignmentPeer::addInstanceToPool($obj, (string) $key);
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
     * @return StudentAssignment|StudentAssignment[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|StudentAssignment[]|mixed the list of results, formatted by the current formatter
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
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StudentAssignmentPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StudentAssignmentPeer::ID, $keys, Criteria::IN);
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
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(StudentAssignmentPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the student_id column
     *
     * Example usage:
     * <code>
     * $query->filterByStudentId(1234); // WHERE student_id = 1234
     * $query->filterByStudentId(array(12, 34)); // WHERE student_id IN (12, 34)
     * $query->filterByStudentId(array('min' => 12)); // WHERE student_id > 12
     * </code>
     *
     * @see       filterByStudent()
     *
     * @param     mixed $studentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByStudentId($studentId = null, $comparison = null)
    {
        if (is_array($studentId)) {
            $useMinMax = false;
            if (isset($studentId['min'])) {
                $this->addUsingAlias(StudentAssignmentPeer::STUDENT_ID, $studentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($studentId['max'])) {
                $this->addUsingAlias(StudentAssignmentPeer::STUDENT_ID, $studentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentAssignmentPeer::STUDENT_ID, $studentId, $comparison);
    }

    /**
     * Filter the query on the assignment_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAssignmentId(1234); // WHERE assignment_id = 1234
     * $query->filterByAssignmentId(array(12, 34)); // WHERE assignment_id IN (12, 34)
     * $query->filterByAssignmentId(array('min' => 12)); // WHERE assignment_id > 12
     * </code>
     *
     * @see       filterByAssignment()
     *
     * @param     mixed $assignmentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByAssignmentId($assignmentId = null, $comparison = null)
    {
        if (is_array($assignmentId)) {
            $useMinMax = false;
            if (isset($assignmentId['min'])) {
                $this->addUsingAlias(StudentAssignmentPeer::ASSIGNMENT_ID, $assignmentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($assignmentId['max'])) {
                $this->addUsingAlias(StudentAssignmentPeer::ASSIGNMENT_ID, $assignmentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentAssignmentPeer::ASSIGNMENT_ID, $assignmentId, $comparison);
    }

    /**
     * Filter the query on the is_submitted column
     *
     * Example usage:
     * <code>
     * $query->filterByIsSubmitted(true); // WHERE is_submitted = true
     * $query->filterByIsSubmitted('yes'); // WHERE is_submitted = true
     * </code>
     *
     * @param     boolean|string $isSubmitted The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByIsSubmitted($isSubmitted = null, $comparison = null)
    {
        if (is_string($isSubmitted)) {
            $isSubmitted = in_array(strtolower($isSubmitted), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(StudentAssignmentPeer::IS_SUBMITTED, $isSubmitted, $comparison);
    }

    /**
     * Filter the query on the grading column
     *
     * Example usage:
     * <code>
     * $query->filterByGrading('fooValue');   // WHERE grading = 'fooValue'
     * $query->filterByGrading('%fooValue%'); // WHERE grading LIKE '%fooValue%'
     * </code>
     *
     * @param     string $grading The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByGrading($grading = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($grading)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $grading)) {
                $grading = str_replace('*', '%', $grading);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StudentAssignmentPeer::GRADING, $grading, $comparison);
    }

    /**
     * Filter the query on the grading_comment column
     *
     * Example usage:
     * <code>
     * $query->filterByGradingComment('fooValue');   // WHERE grading_comment = 'fooValue'
     * $query->filterByGradingComment('%fooValue%'); // WHERE grading_comment LIKE '%fooValue%'
     * </code>
     *
     * @param     string $gradingComment The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByGradingComment($gradingComment = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($gradingComment)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $gradingComment)) {
                $gradingComment = str_replace('*', '%', $gradingComment);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StudentAssignmentPeer::GRADING_COMMENT, $gradingComment, $comparison);
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
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(StudentAssignmentPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(StudentAssignmentPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentAssignmentPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query by a related Student object
     *
     * @param   Student|PropelObjectCollection $student The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentAssignmentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByStudent($student, $comparison = null)
    {
        if ($student instanceof Student) {
            return $this
                ->addUsingAlias(StudentAssignmentPeer::STUDENT_ID, $student->getId(), $comparison);
        } elseif ($student instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentAssignmentPeer::STUDENT_ID, $student->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByStudent() only accepts arguments of type Student or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Student relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function joinStudent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Student');

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
            $this->addJoinObject($join, 'Student');
        }

        return $this;
    }

    /**
     * Use the Student relation Student object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\StudentQuery A secondary query class using the current class as primary query
     */
    public function useStudentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinStudent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Student', '\Zerebral\BusinessBundle\Model\User\StudentQuery');
    }

    /**
     * Filter the query by a related Assignment object
     *
     * @param   Assignment|PropelObjectCollection $assignment The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentAssignmentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByAssignment($assignment, $comparison = null)
    {
        if ($assignment instanceof Assignment) {
            return $this
                ->addUsingAlias(StudentAssignmentPeer::ASSIGNMENT_ID, $assignment->getId(), $comparison);
        } elseif ($assignment instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentAssignmentPeer::ASSIGNMENT_ID, $assignment->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return StudentAssignmentQuery The current query, for fluid interface
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
     * Filter the query by a related FileReferences object
     *
     * @param   FileReferences|PropelObjectCollection $fileReferences  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentAssignmentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFileReferences($fileReferences, $comparison = null)
    {
        if ($fileReferences instanceof FileReferences) {
            return $this
                ->addUsingAlias(StudentAssignmentPeer::ID, $fileReferences->getreferenceId(), $comparison);
        } elseif ($fileReferences instanceof PropelObjectCollection) {
            return $this
                ->useFileReferencesQuery()
                ->filterByPrimaryKeys($fileReferences->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByFileReferences() only accepts arguments of type FileReferences or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FileReferences relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function joinFileReferences($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FileReferences');

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
            $this->addJoinObject($join, 'FileReferences');
        }

        return $this;
    }

    /**
     * Use the FileReferences relation FileReferences object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\File\FileReferencesQuery A secondary query class using the current class as primary query
     */
    public function useFileReferencesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFileReferences($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FileReferences', '\Zerebral\BusinessBundle\Model\File\FileReferencesQuery');
    }

    /**
     * Filter the query by a related File object
     * using the file_references table as cross reference
     *
     * @param   File $file the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByFile($file, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useFileReferencesQuery()
            ->filterByFile($file, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Assignment object
     * using the file_references table as cross reference
     *
     * @param   Assignment $assignment the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterByassignmentReferenceId($assignment, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useFileReferencesQuery()
            ->filterByassignmentReferenceId($assignment, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Message object
     * using the file_references table as cross reference
     *
     * @param   Message $message the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentAssignmentQuery The current query, for fluid interface
     */
    public function filterBymessageReferenceId($message, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useFileReferencesQuery()
            ->filterBymessageReferenceId($message, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   StudentAssignment $studentAssignment Object to remove from the list of results
     *
     * @return StudentAssignmentQuery The current query, for fluid interface
     */
    public function prune($studentAssignment = null)
    {
        if ($studentAssignment) {
            $this->addUsingAlias(StudentAssignmentPeer::ID, $studentAssignment->getId(), Criteria::NOT_EQUAL);
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
