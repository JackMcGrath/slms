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
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignment;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CourseStudent;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\StudentPeer;
use Zerebral\BusinessBundle\Model\User\StudentQuery;
use Zerebral\BusinessBundle\Model\User\User;

/**
 * @method StudentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method StudentQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method StudentQuery orderByBio($order = Criteria::ASC) Order by the bio column
 * @method StudentQuery orderByActivities($order = Criteria::ASC) Order by the activities column
 * @method StudentQuery orderByInterests($order = Criteria::ASC) Order by the interests column
 *
 * @method StudentQuery groupById() Group by the id column
 * @method StudentQuery groupByUserId() Group by the user_id column
 * @method StudentQuery groupByBio() Group by the bio column
 * @method StudentQuery groupByActivities() Group by the activities column
 * @method StudentQuery groupByInterests() Group by the interests column
 *
 * @method StudentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method StudentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method StudentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method StudentQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method StudentQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method StudentQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method StudentQuery leftJoinStudentAssignment($relationAlias = null) Adds a LEFT JOIN clause to the query using the StudentAssignment relation
 * @method StudentQuery rightJoinStudentAssignment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the StudentAssignment relation
 * @method StudentQuery innerJoinStudentAssignment($relationAlias = null) Adds a INNER JOIN clause to the query using the StudentAssignment relation
 *
 * @method StudentQuery leftJoinCourseStudent($relationAlias = null) Adds a LEFT JOIN clause to the query using the CourseStudent relation
 * @method StudentQuery rightJoinCourseStudent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CourseStudent relation
 * @method StudentQuery innerJoinCourseStudent($relationAlias = null) Adds a INNER JOIN clause to the query using the CourseStudent relation
 *
 * @method Student findOne(PropelPDO $con = null) Return the first Student matching the query
 * @method Student findOneOrCreate(PropelPDO $con = null) Return the first Student matching the query, or a new Student object populated from the query conditions when no match is found
 *
 * @method Student findOneByUserId(int $user_id) Return the first Student filtered by the user_id column
 * @method Student findOneByBio(string $bio) Return the first Student filtered by the bio column
 * @method Student findOneByActivities(string $activities) Return the first Student filtered by the activities column
 * @method Student findOneByInterests(string $interests) Return the first Student filtered by the interests column
 *
 * @method array findById(int $id) Return Student objects filtered by the id column
 * @method array findByUserId(int $user_id) Return Student objects filtered by the user_id column
 * @method array findByBio(string $bio) Return Student objects filtered by the bio column
 * @method array findByActivities(string $activities) Return Student objects filtered by the activities column
 * @method array findByInterests(string $interests) Return Student objects filtered by the interests column
 */
abstract class BaseStudentQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseStudentQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\User\\Student', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new StudentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     StudentQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return StudentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof StudentQuery) {
            return $criteria;
        }
        $query = new StudentQuery();
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
     * @return   Student|Student[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = StudentPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(StudentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Student A model object, or null if the key is not found
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
     * @return   Student A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `user_id`, `bio`, `activities`, `interests` FROM `students` WHERE `id` = :p0';
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
            $obj = new Student();
            $obj->hydrate($row);
            StudentPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Student|Student[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Student[]|mixed the list of results, formatted by the current formatter
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
     * @return StudentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StudentPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return StudentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StudentPeer::ID, $keys, Criteria::IN);
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
     * @return StudentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(StudentPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id > 12
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
     * @return StudentQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(StudentPeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(StudentPeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentPeer::USER_ID, $userId, $comparison);
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
     * @return StudentQuery The current query, for fluid interface
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

        return $this->addUsingAlias(StudentPeer::BIO, $bio, $comparison);
    }

    /**
     * Filter the query on the activities column
     *
     * Example usage:
     * <code>
     * $query->filterByActivities('fooValue');   // WHERE activities = 'fooValue'
     * $query->filterByActivities('%fooValue%'); // WHERE activities LIKE '%fooValue%'
     * </code>
     *
     * @param     string $activities The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentQuery The current query, for fluid interface
     */
    public function filterByActivities($activities = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($activities)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $activities)) {
                $activities = str_replace('*', '%', $activities);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StudentPeer::ACTIVITIES, $activities, $comparison);
    }

    /**
     * Filter the query on the interests column
     *
     * Example usage:
     * <code>
     * $query->filterByInterests('fooValue');   // WHERE interests = 'fooValue'
     * $query->filterByInterests('%fooValue%'); // WHERE interests LIKE '%fooValue%'
     * </code>
     *
     * @param     string $interests The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentQuery The current query, for fluid interface
     */
    public function filterByInterests($interests = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($interests)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $interests)) {
                $interests = str_replace('*', '%', $interests);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(StudentPeer::INTERESTS, $interests, $comparison);
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(StudentPeer::USER_ID, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentPeer::USER_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return StudentQuery The current query, for fluid interface
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
     * Filter the query by a related StudentAssignment object
     *
     * @param   StudentAssignment|PropelObjectCollection $studentAssignment  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByStudentAssignment($studentAssignment, $comparison = null)
    {
        if ($studentAssignment instanceof StudentAssignment) {
            return $this
                ->addUsingAlias(StudentPeer::ID, $studentAssignment->getStudentId(), $comparison);
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
     * @return StudentQuery The current query, for fluid interface
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
     * Filter the query by a related CourseStudent object
     *
     * @param   CourseStudent|PropelObjectCollection $courseStudent  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourseStudent($courseStudent, $comparison = null)
    {
        if ($courseStudent instanceof CourseStudent) {
            return $this
                ->addUsingAlias(StudentPeer::ID, $courseStudent->getStudentId(), $comparison);
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
     * @return StudentQuery The current query, for fluid interface
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
     * Filter the query by a related Assignment object
     * using the student_assignments table as cross reference
     *
     * @param   Assignment $assignment the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentQuery The current query, for fluid interface
     */
    public function filterByAssignment($assignment, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useStudentAssignmentQuery()
            ->filterByAssignment($assignment, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Course object
     * using the course_students table as cross reference
     *
     * @param   Course $course the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   StudentQuery The current query, for fluid interface
     */
    public function filterByCourse($course, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useCourseStudentQuery()
            ->filterByCourse($course, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Student $student Object to remove from the list of results
     *
     * @return StudentQuery The current query, for fluid interface
     */
    public function prune($student = null)
    {
        if ($student) {
            $this->addUsingAlias(StudentPeer::ID, $student->getId(), Criteria::NOT_EQUAL);
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
