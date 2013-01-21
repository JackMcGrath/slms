<?php

namespace Zerebral\BusinessBundle\Model\Feed\om;

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
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Feed\FeedComment;
use Zerebral\BusinessBundle\Model\Feed\FeedContent;
use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\Feed\FeedItemPeer;
use Zerebral\BusinessBundle\Model\Feed\FeedItemQuery;
use Zerebral\BusinessBundle\Model\User\User;

/**
 * @method FeedItemQuery orderById($order = Criteria::ASC) Order by the id column
 * @method FeedItemQuery orderByAssignmentId($order = Criteria::ASC) Order by the assignment_id column
 * @method FeedItemQuery orderByCourseId($order = Criteria::ASC) Order by the course_id column
 * @method FeedItemQuery orderByFeedContentId($order = Criteria::ASC) Order by the feed_content_id column
 * @method FeedItemQuery orderByCreatedBy($order = Criteria::ASC) Order by the created_by column
 * @method FeedItemQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 *
 * @method FeedItemQuery groupById() Group by the id column
 * @method FeedItemQuery groupByAssignmentId() Group by the assignment_id column
 * @method FeedItemQuery groupByCourseId() Group by the course_id column
 * @method FeedItemQuery groupByFeedContentId() Group by the feed_content_id column
 * @method FeedItemQuery groupByCreatedBy() Group by the created_by column
 * @method FeedItemQuery groupByCreatedAt() Group by the created_at column
 *
 * @method FeedItemQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FeedItemQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FeedItemQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FeedItemQuery leftJoinFeedContent($relationAlias = null) Adds a LEFT JOIN clause to the query using the FeedContent relation
 * @method FeedItemQuery rightJoinFeedContent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FeedContent relation
 * @method FeedItemQuery innerJoinFeedContent($relationAlias = null) Adds a INNER JOIN clause to the query using the FeedContent relation
 *
 * @method FeedItemQuery leftJoinAssignment($relationAlias = null) Adds a LEFT JOIN clause to the query using the Assignment relation
 * @method FeedItemQuery rightJoinAssignment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Assignment relation
 * @method FeedItemQuery innerJoinAssignment($relationAlias = null) Adds a INNER JOIN clause to the query using the Assignment relation
 *
 * @method FeedItemQuery leftJoinCourse($relationAlias = null) Adds a LEFT JOIN clause to the query using the Course relation
 * @method FeedItemQuery rightJoinCourse($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Course relation
 * @method FeedItemQuery innerJoinCourse($relationAlias = null) Adds a INNER JOIN clause to the query using the Course relation
 *
 * @method FeedItemQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method FeedItemQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method FeedItemQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method FeedItemQuery leftJoinFeedComment($relationAlias = null) Adds a LEFT JOIN clause to the query using the FeedComment relation
 * @method FeedItemQuery rightJoinFeedComment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FeedComment relation
 * @method FeedItemQuery innerJoinFeedComment($relationAlias = null) Adds a INNER JOIN clause to the query using the FeedComment relation
 *
 * @method FeedItem findOne(PropelPDO $con = null) Return the first FeedItem matching the query
 * @method FeedItem findOneOrCreate(PropelPDO $con = null) Return the first FeedItem matching the query, or a new FeedItem object populated from the query conditions when no match is found
 *
 * @method FeedItem findOneByAssignmentId(int $assignment_id) Return the first FeedItem filtered by the assignment_id column
 * @method FeedItem findOneByCourseId(int $course_id) Return the first FeedItem filtered by the course_id column
 * @method FeedItem findOneByFeedContentId(int $feed_content_id) Return the first FeedItem filtered by the feed_content_id column
 * @method FeedItem findOneByCreatedBy(int $created_by) Return the first FeedItem filtered by the created_by column
 * @method FeedItem findOneByCreatedAt(string $created_at) Return the first FeedItem filtered by the created_at column
 *
 * @method array findById(int $id) Return FeedItem objects filtered by the id column
 * @method array findByAssignmentId(int $assignment_id) Return FeedItem objects filtered by the assignment_id column
 * @method array findByCourseId(int $course_id) Return FeedItem objects filtered by the course_id column
 * @method array findByFeedContentId(int $feed_content_id) Return FeedItem objects filtered by the feed_content_id column
 * @method array findByCreatedBy(int $created_by) Return FeedItem objects filtered by the created_by column
 * @method array findByCreatedAt(string $created_at) Return FeedItem objects filtered by the created_at column
 */
abstract class BaseFeedItemQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFeedItemQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new FeedItemQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     FeedItemQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FeedItemQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FeedItemQuery) {
            return $criteria;
        }
        $query = new FeedItemQuery();
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
     * @return   FeedItem|FeedItem[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FeedItemPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FeedItemPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   FeedItem A model object, or null if the key is not found
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
     * @return   FeedItem A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `assignment_id`, `course_id`, `feed_content_id`, `created_by`, `created_at` FROM `feed_items` WHERE `id` = :p0';
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
            $obj = new FeedItem();
            $obj->hydrate($row);
            FeedItemPeer::addInstanceToPool($obj, (string) $key);
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
     * @return FeedItem|FeedItem[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FeedItem[]|mixed the list of results, formatted by the current formatter
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
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FeedItemPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FeedItemPeer::ID, $keys, Criteria::IN);
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
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FeedItemPeer::ID, $id, $comparison);
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
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function filterByAssignmentId($assignmentId = null, $comparison = null)
    {
        if (is_array($assignmentId)) {
            $useMinMax = false;
            if (isset($assignmentId['min'])) {
                $this->addUsingAlias(FeedItemPeer::ASSIGNMENT_ID, $assignmentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($assignmentId['max'])) {
                $this->addUsingAlias(FeedItemPeer::ASSIGNMENT_ID, $assignmentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedItemPeer::ASSIGNMENT_ID, $assignmentId, $comparison);
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
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function filterByCourseId($courseId = null, $comparison = null)
    {
        if (is_array($courseId)) {
            $useMinMax = false;
            if (isset($courseId['min'])) {
                $this->addUsingAlias(FeedItemPeer::COURSE_ID, $courseId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($courseId['max'])) {
                $this->addUsingAlias(FeedItemPeer::COURSE_ID, $courseId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedItemPeer::COURSE_ID, $courseId, $comparison);
    }

    /**
     * Filter the query on the feed_content_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFeedContentId(1234); // WHERE feed_content_id = 1234
     * $query->filterByFeedContentId(array(12, 34)); // WHERE feed_content_id IN (12, 34)
     * $query->filterByFeedContentId(array('min' => 12)); // WHERE feed_content_id > 12
     * </code>
     *
     * @see       filterByFeedContent()
     *
     * @param     mixed $feedContentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function filterByFeedContentId($feedContentId = null, $comparison = null)
    {
        if (is_array($feedContentId)) {
            $useMinMax = false;
            if (isset($feedContentId['min'])) {
                $this->addUsingAlias(FeedItemPeer::FEED_CONTENT_ID, $feedContentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($feedContentId['max'])) {
                $this->addUsingAlias(FeedItemPeer::FEED_CONTENT_ID, $feedContentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedItemPeer::FEED_CONTENT_ID, $feedContentId, $comparison);
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
     * @see       filterByUser()
     *
     * @param     mixed $createdBy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function filterByCreatedBy($createdBy = null, $comparison = null)
    {
        if (is_array($createdBy)) {
            $useMinMax = false;
            if (isset($createdBy['min'])) {
                $this->addUsingAlias(FeedItemPeer::CREATED_BY, $createdBy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdBy['max'])) {
                $this->addUsingAlias(FeedItemPeer::CREATED_BY, $createdBy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedItemPeer::CREATED_BY, $createdBy, $comparison);
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
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(FeedItemPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(FeedItemPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedItemPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query by a related FeedContent object
     *
     * @param   FeedContent|PropelObjectCollection $feedContent The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFeedContent($feedContent, $comparison = null)
    {
        if ($feedContent instanceof FeedContent) {
            return $this
                ->addUsingAlias(FeedItemPeer::FEED_CONTENT_ID, $feedContent->getId(), $comparison);
        } elseif ($feedContent instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FeedItemPeer::FEED_CONTENT_ID, $feedContent->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByFeedContent() only accepts arguments of type FeedContent or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FeedContent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function joinFeedContent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FeedContent');

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
            $this->addJoinObject($join, 'FeedContent');
        }

        return $this;
    }

    /**
     * Use the FeedContent relation FeedContent object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Feed\FeedContentQuery A secondary query class using the current class as primary query
     */
    public function useFeedContentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFeedContent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FeedContent', '\Zerebral\BusinessBundle\Model\Feed\FeedContentQuery');
    }

    /**
     * Filter the query by a related Assignment object
     *
     * @param   Assignment|PropelObjectCollection $assignment The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByAssignment($assignment, $comparison = null)
    {
        if ($assignment instanceof Assignment) {
            return $this
                ->addUsingAlias(FeedItemPeer::ASSIGNMENT_ID, $assignment->getId(), $comparison);
        } elseif ($assignment instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FeedItemPeer::ASSIGNMENT_ID, $assignment->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function joinAssignment($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useAssignmentQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAssignment($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Assignment', '\Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery');
    }

    /**
     * Filter the query by a related Course object
     *
     * @param   Course|PropelObjectCollection $course The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByCourse($course, $comparison = null)
    {
        if ($course instanceof Course) {
            return $this
                ->addUsingAlias(FeedItemPeer::COURSE_ID, $course->getId(), $comparison);
        } elseif ($course instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FeedItemPeer::COURSE_ID, $course->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function joinCourse($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useCourseQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCourse($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Course', '\Zerebral\BusinessBundle\Model\Course\CourseQuery');
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(FeedItemPeer::CREATED_BY, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FeedItemPeer::CREATED_BY, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return FeedItemQuery The current query, for fluid interface
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
     * Filter the query by a related FeedComment object
     *
     * @param   FeedComment|PropelObjectCollection $feedComment  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedItemQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFeedComment($feedComment, $comparison = null)
    {
        if ($feedComment instanceof FeedComment) {
            return $this
                ->addUsingAlias(FeedItemPeer::ID, $feedComment->getFeedItemId(), $comparison);
        } elseif ($feedComment instanceof PropelObjectCollection) {
            return $this
                ->useFeedCommentQuery()
                ->filterByPrimaryKeys($feedComment->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByFeedComment() only accepts arguments of type FeedComment or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FeedComment relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function joinFeedComment($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FeedComment');

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
            $this->addJoinObject($join, 'FeedComment');
        }

        return $this;
    }

    /**
     * Use the FeedComment relation FeedComment object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Feed\FeedCommentQuery A secondary query class using the current class as primary query
     */
    public function useFeedCommentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFeedComment($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FeedComment', '\Zerebral\BusinessBundle\Model\Feed\FeedCommentQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   FeedItem $feedItem Object to remove from the list of results
     *
     * @return FeedItemQuery The current query, for fluid interface
     */
    public function prune($feedItem = null)
    {
        if ($feedItem) {
            $this->addUsingAlias(FeedItemPeer::ID, $feedItem->getId(), Criteria::NOT_EQUAL);
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
