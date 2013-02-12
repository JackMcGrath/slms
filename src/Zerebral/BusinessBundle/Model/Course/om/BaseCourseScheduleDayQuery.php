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
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CourseScheduleDay;
use Zerebral\BusinessBundle\Model\Course\CourseScheduleDayPeer;
use Zerebral\BusinessBundle\Model\Course\CourseScheduleDayQuery;

/**
 * @method CourseScheduleDayQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CourseScheduleDayQuery orderByCourseId($order = Criteria::ASC) Order by the course_id column
 * @method CourseScheduleDayQuery orderByWeekDay($order = Criteria::ASC) Order by the week_day column
 * @method CourseScheduleDayQuery orderByTimeFrom($order = Criteria::ASC) Order by the time_from column
 * @method CourseScheduleDayQuery orderByTimeTo($order = Criteria::ASC) Order by the time_to column
 *
 * @method CourseScheduleDayQuery groupById() Group by the id column
 * @method CourseScheduleDayQuery groupByCourseId() Group by the course_id column
 * @method CourseScheduleDayQuery groupByWeekDay() Group by the week_day column
 * @method CourseScheduleDayQuery groupByTimeFrom() Group by the time_from column
 * @method CourseScheduleDayQuery groupByTimeTo() Group by the time_to column
 *
 * @method CourseScheduleDayQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CourseScheduleDayQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CourseScheduleDayQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CourseScheduleDayQuery leftJoinCourse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Course relation
 * @method CourseScheduleDayQuery rightJoinCourse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Course relation
 * @method CourseScheduleDayQuery innerJoinCourse($relationAlias = null) Adds a INNER JOIN clause to the query using the Course relation
 *
 * @method CourseScheduleDay findOne(PropelPDO $con = null) Return the first CourseScheduleDay matching the query
 * @method CourseScheduleDay findOneOrCreate(PropelPDO $con = null) Return the first CourseScheduleDay matching the query, or a new CourseScheduleDay object populated from the query conditions when no match is found
 *
 * @method CourseScheduleDay findOneById(int $id) Return the first CourseScheduleDay filtered by the id column
 * @method CourseScheduleDay findOneByCourseId(int $course_id) Return the first CourseScheduleDay filtered by the course_id column
 * @method CourseScheduleDay findOneByWeekDay(string $week_day) Return the first CourseScheduleDay filtered by the week_day column
 * @method CourseScheduleDay findOneByTimeFrom(string $time_from) Return the first CourseScheduleDay filtered by the time_from column
 * @method CourseScheduleDay findOneByTimeTo(string $time_to) Return the first CourseScheduleDay filtered by the time_to column
 *
 * @method array findById(int $id) Return CourseScheduleDay objects filtered by the id column
 * @method array findByCourseId(int $course_id) Return CourseScheduleDay objects filtered by the course_id column
 * @method array findByWeekDay(string $week_day) Return CourseScheduleDay objects filtered by the week_day column
 * @method array findByTimeFrom(string $time_from) Return CourseScheduleDay objects filtered by the time_from column
 * @method array findByTimeTo(string $time_to) Return CourseScheduleDay objects filtered by the time_to column
 */
abstract class BaseCourseScheduleDayQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCourseScheduleDayQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Course\\CourseScheduleDay', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new CourseScheduleDayQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     CourseScheduleDayQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CourseScheduleDayQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CourseScheduleDayQuery) {
            return $criteria;
        }
        $query = new CourseScheduleDayQuery();
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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$id, $course_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   CourseScheduleDay|CourseScheduleDay[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CourseScheduleDayPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CourseScheduleDayPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return   CourseScheduleDay A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `course_id`, `week_day`, `time_from`, `time_to` FROM `course_schedule_days` WHERE `id` = :p0 AND `course_id` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new CourseScheduleDay();
            $obj->hydrate($row);
            CourseScheduleDayPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return CourseScheduleDay|CourseScheduleDay[]|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|CourseScheduleDay[]|mixed the list of results, formatted by the current formatter
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
     * @return CourseScheduleDayQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(CourseScheduleDayPeer::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(CourseScheduleDayPeer::COURSE_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CourseScheduleDayQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(CourseScheduleDayPeer::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(CourseScheduleDayPeer::COURSE_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @return CourseScheduleDayQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(CourseScheduleDayPeer::ID, $id, $comparison);
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
     * @return CourseScheduleDayQuery The current query, for fluid interface
     */
    public function filterByCourseId($courseId = null, $comparison = null)
    {
        if (is_array($courseId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(CourseScheduleDayPeer::COURSE_ID, $courseId, $comparison);
    }

    /**
     * Filter the query on the week_day column
     *
     * Example usage:
     * <code>
     * $query->filterByWeekDay('fooValue');   // WHERE week_day = 'fooValue'
     * $query->filterByWeekDay('%fooValue%'); // WHERE week_day LIKE '%fooValue%'
     * </code>
     *
     * @param     string $weekDay The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseScheduleDayQuery The current query, for fluid interface
     */
    public function filterByWeekDay($weekDay = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($weekDay)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $weekDay)) {
                $weekDay = str_replace('*', '%', $weekDay);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CourseScheduleDayPeer::WEEK_DAY, $weekDay, $comparison);
    }

    /**
     * Filter the query on the time_from column
     *
     * Example usage:
     * <code>
     * $query->filterByTimeFrom('2011-03-14'); // WHERE time_from = '2011-03-14'
     * $query->filterByTimeFrom('now'); // WHERE time_from = '2011-03-14'
     * $query->filterByTimeFrom(array('max' => 'yesterday')); // WHERE time_from > '2011-03-13'
     * </code>
     *
     * @param     mixed $timeFrom The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseScheduleDayQuery The current query, for fluid interface
     */
    public function filterByTimeFrom($timeFrom = null, $comparison = null)
    {
        if (is_array($timeFrom)) {
            $useMinMax = false;
            if (isset($timeFrom['min'])) {
                $this->addUsingAlias(CourseScheduleDayPeer::TIME_FROM, $timeFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($timeFrom['max'])) {
                $this->addUsingAlias(CourseScheduleDayPeer::TIME_FROM, $timeFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CourseScheduleDayPeer::TIME_FROM, $timeFrom, $comparison);
    }

    /**
     * Filter the query on the time_to column
     *
     * Example usage:
     * <code>
     * $query->filterByTimeTo('2011-03-14'); // WHERE time_to = '2011-03-14'
     * $query->filterByTimeTo('now'); // WHERE time_to = '2011-03-14'
     * $query->filterByTimeTo(array('max' => 'yesterday')); // WHERE time_to > '2011-03-13'
     * </code>
     *
     * @param     mixed $timeTo The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CourseScheduleDayQuery The current query, for fluid interface
     */
    public function filterByTimeTo($timeTo = null, $comparison = null)
    {
        if (is_array($timeTo)) {
            $useMinMax = false;
            if (isset($timeTo['min'])) {
                $this->addUsingAlias(CourseScheduleDayPeer::TIME_TO, $timeTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($timeTo['max'])) {
                $this->addUsingAlias(CourseScheduleDayPeer::TIME_TO, $timeTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CourseScheduleDayPeer::TIME_TO, $timeTo, $comparison);
    }

    /**
     * Filter the query by a related Course object
     *
     * @param   Course|PropelObjectCollection $course The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   CourseScheduleDayQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourse($course, $comparison = null)
    {
        if ($course instanceof Course) {
            return $this
                ->addUsingAlias(CourseScheduleDayPeer::COURSE_ID, $course->getId(), $comparison);
        } elseif ($course instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CourseScheduleDayPeer::COURSE_ID, $course->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return CourseScheduleDayQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   CourseScheduleDay $courseScheduleDay Object to remove from the list of results
     *
     * @return CourseScheduleDayQuery The current query, for fluid interface
     */
    public function prune($courseScheduleDay = null)
    {
        if ($courseScheduleDay) {
            $this->addCond('pruneCond0', $this->getAliasedColName(CourseScheduleDayPeer::ID), $courseScheduleDay->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(CourseScheduleDayPeer::COURSE_ID), $courseScheduleDay->getCourseId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
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
