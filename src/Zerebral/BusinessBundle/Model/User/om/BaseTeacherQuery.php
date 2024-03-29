<?php

namespace Zerebral\BusinessBundle\Model\User\om;

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
use Zerebral\BusinessBundle\Model\Attendance\Attendance;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CourseTeacher;
use Zerebral\BusinessBundle\Model\Course\Discipline;
use Zerebral\BusinessBundle\Model\Material\CourseMaterial;
use Zerebral\BusinessBundle\Model\User\Teacher;
use Zerebral\BusinessBundle\Model\User\TeacherPeer;
use Zerebral\BusinessBundle\Model\User\TeacherQuery;
use Zerebral\BusinessBundle\Model\User\User;

/**
 * @method TeacherQuery orderById($order = Criteria::ASC) Order by the id column
 * @method TeacherQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method TeacherQuery orderByBio($order = Criteria::ASC) Order by the bio column
 * @method TeacherQuery orderBySubjects($order = Criteria::ASC) Order by the subjects column
 * @method TeacherQuery orderByGrades($order = Criteria::ASC) Order by the grades column
 *
 * @method TeacherQuery groupById() Group by the id column
 * @method TeacherQuery groupByUserId() Group by the user_id column
 * @method TeacherQuery groupByBio() Group by the bio column
 * @method TeacherQuery groupBySubjects() Group by the subjects column
 * @method TeacherQuery groupByGrades() Group by the grades column
 *
 * @method TeacherQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method TeacherQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method TeacherQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method TeacherQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method TeacherQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method TeacherQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method TeacherQuery leftJoinAssignmentCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the AssignmentCategory relation
 * @method TeacherQuery rightJoinAssignmentCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AssignmentCategory relation
 * @method TeacherQuery innerJoinAssignmentCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the AssignmentCategory relation
 *
 * @method TeacherQuery leftJoinAssignment($relationAlias = null) Adds a LEFT JOIN clause to the query using the Assignment relation
 * @method TeacherQuery rightJoinAssignment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Assignment relation
 * @method TeacherQuery innerJoinAssignment($relationAlias = null) Adds a INNER JOIN clause to the query using the Assignment relation
 *
 * @method TeacherQuery leftJoinAttendance($relationAlias = null) Adds a LEFT JOIN clause to the query using the Attendance relation
 * @method TeacherQuery rightJoinAttendance($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Attendance relation
 * @method TeacherQuery innerJoinAttendance($relationAlias = null) Adds a INNER JOIN clause to the query using the Attendance relation
 *
 * @method TeacherQuery leftJoinCreatedByTeacher($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreatedByTeacher relation
 * @method TeacherQuery rightJoinCreatedByTeacher($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreatedByTeacher relation
 * @method TeacherQuery innerJoinCreatedByTeacher($relationAlias = null) Adds a INNER JOIN clause to the query using the CreatedByTeacher relation
 *
 * @method TeacherQuery leftJoinDiscipline($relationAlias = null) Adds a LEFT JOIN clause to the query using the Discipline relation
 * @method TeacherQuery rightJoinDiscipline($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Discipline relation
 * @method TeacherQuery innerJoinDiscipline($relationAlias = null) Adds a INNER JOIN clause to the query using the Discipline relation
 *
 * @method TeacherQuery leftJoinCourseTeacher($relationAlias = null) Adds a LEFT JOIN clause to the query using the CourseTeacher relation
 * @method TeacherQuery rightJoinCourseTeacher($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CourseTeacher relation
 * @method TeacherQuery innerJoinCourseTeacher($relationAlias = null) Adds a INNER JOIN clause to the query using the CourseTeacher relation
 *
 * @method TeacherQuery leftJoinCourseMaterial($relationAlias = null) Adds a LEFT JOIN clause to the query using the CourseMaterial relation
 * @method TeacherQuery rightJoinCourseMaterial($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CourseMaterial relation
 * @method TeacherQuery innerJoinCourseMaterial($relationAlias = null) Adds a INNER JOIN clause to the query using the CourseMaterial relation
 *
 * @method Teacher findOne(PropelPDO $con = null) Return the first Teacher matching the query
 * @method Teacher findOneOrCreate(PropelPDO $con = null) Return the first Teacher matching the query, or a new Teacher object populated from the query conditions when no match is found
 *
 * @method Teacher findOneByUserId(int $user_id) Return the first Teacher filtered by the user_id column
 * @method Teacher findOneByBio(string $bio) Return the first Teacher filtered by the bio column
 * @method Teacher findOneBySubjects(string $subjects) Return the first Teacher filtered by the subjects column
 * @method Teacher findOneByGrades(string $grades) Return the first Teacher filtered by the grades column
 *
 * @method array findById(int $id) Return Teacher objects filtered by the id column
 * @method array findByUserId(int $user_id) Return Teacher objects filtered by the user_id column
 * @method array findByBio(string $bio) Return Teacher objects filtered by the bio column
 * @method array findBySubjects(string $subjects) Return Teacher objects filtered by the subjects column
 * @method array findByGrades(string $grades) Return Teacher objects filtered by the grades column
 */
abstract class BaseTeacherQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseTeacherQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new TeacherQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   TeacherQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return TeacherQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof TeacherQuery) {
            return $criteria;
        }
        $query = new TeacherQuery();
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
     * @return   Teacher|Teacher[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TeacherPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(TeacherPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Teacher A model object, or null if the key is not found
     * @throws PropelException
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
     * @return                 Teacher A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `user_id`, `bio`, `subjects`, `grades` FROM `teachers` WHERE `id` = :p0';
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
            $obj = new Teacher();
            $obj->hydrate($row);
            TeacherPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Teacher|Teacher[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Teacher[]|mixed the list of results, formatted by the current formatter
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
     * @return TeacherQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TeacherPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TeacherPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TeacherPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TeacherPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TeacherPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id >= 12
     * $query->filterByUserId(array('max' => 12)); // WHERE user_id <= 12
     * </code>
     *
     * @see       filterByUser()
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(TeacherPeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(TeacherPeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TeacherPeer::USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the bio column
     *
     * Example usage:
     * <code>
     * $query->filterByBio('fooValue');   // WHERE bio = 'fooValue'
     * $query->filterByBio('%fooValue%'); // WHERE bio LIKE '%fooValue%'
     * </code>
     *
     * @param     string $bio The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function filterByBio($bio = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($bio)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $bio)) {
                $bio = str_replace('*', '%', $bio);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TeacherPeer::BIO, $bio, $comparison);
    }

    /**
     * Filter the query on the subjects column
     *
     * Example usage:
     * <code>
     * $query->filterBySubjects('fooValue');   // WHERE subjects = 'fooValue'
     * $query->filterBySubjects('%fooValue%'); // WHERE subjects LIKE '%fooValue%'
     * </code>
     *
     * @param     string $subjects The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function filterBySubjects($subjects = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($subjects)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $subjects)) {
                $subjects = str_replace('*', '%', $subjects);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TeacherPeer::SUBJECTS, $subjects, $comparison);
    }

    /**
     * Filter the query on the grades column
     *
     * Example usage:
     * <code>
     * $query->filterByGrades('fooValue');   // WHERE grades = 'fooValue'
     * $query->filterByGrades('%fooValue%'); // WHERE grades LIKE '%fooValue%'
     * </code>
     *
     * @param     string $grades The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function filterByGrades($grades = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($grades)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $grades)) {
                $grades = str_replace('*', '%', $grades);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TeacherPeer::GRADES, $grades, $comparison);
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TeacherQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(TeacherPeer::USER_ID, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TeacherPeer::USER_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUser() only accepts arguments of type User or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the User relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function joinUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('User');

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
            $this->addJoinObject($join, 'User');
        }

        return $this;
    }

    /**
     * Use the User relation User object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\UserQuery A secondary query class using the current class as primary query
     */
    public function useUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'User', '\Zerebral\BusinessBundle\Model\User\UserQuery');
    }

    /**
     * Filter the query by a related AssignmentCategory object
     *
     * @param   AssignmentCategory|PropelObjectCollection $assignmentCategory  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TeacherQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAssignmentCategory($assignmentCategory, $comparison = null)
    {
        if ($assignmentCategory instanceof AssignmentCategory) {
            return $this
                ->addUsingAlias(TeacherPeer::ID, $assignmentCategory->getTeacherId(), $comparison);
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
     * @return TeacherQuery The current query, for fluid interface
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
     * @return                 TeacherQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAssignment($assignment, $comparison = null)
    {
        if ($assignment instanceof Assignment) {
            return $this
                ->addUsingAlias(TeacherPeer::ID, $assignment->getTeacherId(), $comparison);
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
     * @return TeacherQuery The current query, for fluid interface
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
     * Filter the query by a related Attendance object
     *
     * @param   Attendance|PropelObjectCollection $attendance  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TeacherQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAttendance($attendance, $comparison = null)
    {
        if ($attendance instanceof Attendance) {
            return $this
                ->addUsingAlias(TeacherPeer::ID, $attendance->getTeacherId(), $comparison);
        } elseif ($attendance instanceof PropelObjectCollection) {
            return $this
                ->useAttendanceQuery()
                ->filterByPrimaryKeys($attendance->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAttendance() only accepts arguments of type Attendance or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Attendance relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function joinAttendance($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Attendance');

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
            $this->addJoinObject($join, 'Attendance');
        }

        return $this;
    }

    /**
     * Use the Attendance relation Attendance object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Attendance\AttendanceQuery A secondary query class using the current class as primary query
     */
    public function useAttendanceQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAttendance($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Attendance', '\Zerebral\BusinessBundle\Model\Attendance\AttendanceQuery');
    }

    /**
     * Filter the query by a related Course object
     *
     * @param   Course|PropelObjectCollection $course  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TeacherQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCreatedByTeacher($course, $comparison = null)
    {
        if ($course instanceof Course) {
            return $this
                ->addUsingAlias(TeacherPeer::ID, $course->getCreatedBy(), $comparison);
        } elseif ($course instanceof PropelObjectCollection) {
            return $this
                ->useCreatedByTeacherQuery()
                ->filterByPrimaryKeys($course->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCreatedByTeacher() only accepts arguments of type Course or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CreatedByTeacher relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TeacherQuery The current query, for fluid interface
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
     * Use the CreatedByTeacher relation Course object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Course\CourseQuery A secondary query class using the current class as primary query
     */
    public function useCreatedByTeacherQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCreatedByTeacher($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreatedByTeacher', '\Zerebral\BusinessBundle\Model\Course\CourseQuery');
    }

    /**
     * Filter the query by a related Discipline object
     *
     * @param   Discipline|PropelObjectCollection $discipline  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TeacherQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByDiscipline($discipline, $comparison = null)
    {
        if ($discipline instanceof Discipline) {
            return $this
                ->addUsingAlias(TeacherPeer::ID, $discipline->getTeacherId(), $comparison);
        } elseif ($discipline instanceof PropelObjectCollection) {
            return $this
                ->useDisciplineQuery()
                ->filterByPrimaryKeys($discipline->getPrimaryKeys())
                ->endUse();
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
     * @return TeacherQuery The current query, for fluid interface
     */
    public function joinDiscipline($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useDisciplineQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinDiscipline($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Discipline', '\Zerebral\BusinessBundle\Model\Course\DisciplineQuery');
    }

    /**
     * Filter the query by a related CourseTeacher object
     *
     * @param   CourseTeacher|PropelObjectCollection $courseTeacher  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TeacherQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCourseTeacher($courseTeacher, $comparison = null)
    {
        if ($courseTeacher instanceof CourseTeacher) {
            return $this
                ->addUsingAlias(TeacherPeer::ID, $courseTeacher->getTeacherId(), $comparison);
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
     * @return TeacherQuery The current query, for fluid interface
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
     * Filter the query by a related CourseMaterial object
     *
     * @param   CourseMaterial|PropelObjectCollection $courseMaterial  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TeacherQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCourseMaterial($courseMaterial, $comparison = null)
    {
        if ($courseMaterial instanceof CourseMaterial) {
            return $this
                ->addUsingAlias(TeacherPeer::ID, $courseMaterial->getCreatedBy(), $comparison);
        } elseif ($courseMaterial instanceof PropelObjectCollection) {
            return $this
                ->useCourseMaterialQuery()
                ->filterByPrimaryKeys($courseMaterial->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCourseMaterial() only accepts arguments of type CourseMaterial or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CourseMaterial relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function joinCourseMaterial($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CourseMaterial');

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
            $this->addJoinObject($join, 'CourseMaterial');
        }

        return $this;
    }

    /**
     * Use the CourseMaterial relation CourseMaterial object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Material\CourseMaterialQuery A secondary query class using the current class as primary query
     */
    public function useCourseMaterialQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCourseMaterial($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CourseMaterial', '\Zerebral\BusinessBundle\Model\Material\CourseMaterialQuery');
    }

    /**
     * Filter the query by a related Course object
     * using the course_teachers table as cross reference
     *
     * @param   Course $course the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   TeacherQuery The current query, for fluid interface
     */
    public function filterByCourse($course, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useCourseTeacherQuery()
            ->filterByCourse($course, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Teacher $teacher Object to remove from the list of results
     *
     * @return TeacherQuery The current query, for fluid interface
     */
    public function prune($teacher = null)
    {
        if ($teacher) {
            $this->addUsingAlias(TeacherPeer::ID, $teacher->getId(), Criteria::NOT_EQUAL);
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
