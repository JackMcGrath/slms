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
use Zerebral\BusinessBundle\Model\Feed\FeedComment;
use Zerebral\BusinessBundle\Model\Feed\FeedCommentPeer;
use Zerebral\BusinessBundle\Model\Feed\FeedCommentQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedContent;
use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\User\User;

/**
 * @method FeedCommentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method FeedCommentQuery orderByFeedItemId($order = Criteria::ASC) Order by the feed_item_id column
 * @method FeedCommentQuery orderByFeedContentId($order = Criteria::ASC) Order by the feed_content_id column
 * @method FeedCommentQuery orderByCreatedBy($order = Criteria::ASC) Order by the created_by column
 * @method FeedCommentQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 *
 * @method FeedCommentQuery groupById() Group by the id column
 * @method FeedCommentQuery groupByFeedItemId() Group by the feed_item_id column
 * @method FeedCommentQuery groupByFeedContentId() Group by the feed_content_id column
 * @method FeedCommentQuery groupByCreatedBy() Group by the created_by column
 * @method FeedCommentQuery groupByCreatedAt() Group by the created_at column
 *
 * @method FeedCommentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FeedCommentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FeedCommentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FeedCommentQuery leftJoinFeedItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the FeedItem relation
 * @method FeedCommentQuery rightJoinFeedItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FeedItem relation
 * @method FeedCommentQuery innerJoinFeedItem($relationAlias = null) Adds a INNER JOIN clause to the query using the FeedItem relation
 *
 * @method FeedCommentQuery leftJoinFeedContent($relationAlias = null) Adds a LEFT JOIN clause to the query using the FeedContent relation
 * @method FeedCommentQuery rightJoinFeedContent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FeedContent relation
 * @method FeedCommentQuery innerJoinFeedContent($relationAlias = null) Adds a INNER JOIN clause to the query using the FeedContent relation
 *
 * @method FeedCommentQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method FeedCommentQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method FeedCommentQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method FeedComment findOne(PropelPDO $con = null) Return the first FeedComment matching the query
 * @method FeedComment findOneOrCreate(PropelPDO $con = null) Return the first FeedComment matching the query, or a new FeedComment object populated from the query conditions when no match is found
 *
 * @method FeedComment findOneByFeedItemId(int $feed_item_id) Return the first FeedComment filtered by the feed_item_id column
 * @method FeedComment findOneByFeedContentId(int $feed_content_id) Return the first FeedComment filtered by the feed_content_id column
 * @method FeedComment findOneByCreatedBy(int $created_by) Return the first FeedComment filtered by the created_by column
 * @method FeedComment findOneByCreatedAt(string $created_at) Return the first FeedComment filtered by the created_at column
 *
 * @method array findById(int $id) Return FeedComment objects filtered by the id column
 * @method array findByFeedItemId(int $feed_item_id) Return FeedComment objects filtered by the feed_item_id column
 * @method array findByFeedContentId(int $feed_content_id) Return FeedComment objects filtered by the feed_content_id column
 * @method array findByCreatedBy(int $created_by) Return FeedComment objects filtered by the created_by column
 * @method array findByCreatedAt(string $created_at) Return FeedComment objects filtered by the created_at column
 */
abstract class BaseFeedCommentQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFeedCommentQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedComment', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new FeedCommentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     FeedCommentQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FeedCommentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FeedCommentQuery) {
            return $criteria;
        }
        $query = new FeedCommentQuery();
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
     * @return   FeedComment|FeedComment[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FeedCommentPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FeedCommentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   FeedComment A model object, or null if the key is not found
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
     * @return   FeedComment A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `feed_item_id`, `feed_content_id`, `created_by`, `created_at` FROM `feed_comments` WHERE `id` = :p0';
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
            $obj = new FeedComment();
            $obj->hydrate($row);
            FeedCommentPeer::addInstanceToPool($obj, (string) $key);
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
     * @return FeedComment|FeedComment[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FeedComment[]|mixed the list of results, formatted by the current formatter
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
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FeedCommentPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FeedCommentPeer::ID, $keys, Criteria::IN);
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
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FeedCommentPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the feed_item_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFeedItemId(1234); // WHERE feed_item_id = 1234
     * $query->filterByFeedItemId(array(12, 34)); // WHERE feed_item_id IN (12, 34)
     * $query->filterByFeedItemId(array('min' => 12)); // WHERE feed_item_id > 12
     * </code>
     *
     * @see       filterByFeedItem()
     *
     * @param     mixed $feedItemId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function filterByFeedItemId($feedItemId = null, $comparison = null)
    {
        if (is_array($feedItemId)) {
            $useMinMax = false;
            if (isset($feedItemId['min'])) {
                $this->addUsingAlias(FeedCommentPeer::FEED_ITEM_ID, $feedItemId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($feedItemId['max'])) {
                $this->addUsingAlias(FeedCommentPeer::FEED_ITEM_ID, $feedItemId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedCommentPeer::FEED_ITEM_ID, $feedItemId, $comparison);
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
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function filterByFeedContentId($feedContentId = null, $comparison = null)
    {
        if (is_array($feedContentId)) {
            $useMinMax = false;
            if (isset($feedContentId['min'])) {
                $this->addUsingAlias(FeedCommentPeer::FEED_CONTENT_ID, $feedContentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($feedContentId['max'])) {
                $this->addUsingAlias(FeedCommentPeer::FEED_CONTENT_ID, $feedContentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedCommentPeer::FEED_CONTENT_ID, $feedContentId, $comparison);
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
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function filterByCreatedBy($createdBy = null, $comparison = null)
    {
        if (is_array($createdBy)) {
            $useMinMax = false;
            if (isset($createdBy['min'])) {
                $this->addUsingAlias(FeedCommentPeer::CREATED_BY, $createdBy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdBy['max'])) {
                $this->addUsingAlias(FeedCommentPeer::CREATED_BY, $createdBy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedCommentPeer::CREATED_BY, $createdBy, $comparison);
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
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(FeedCommentPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(FeedCommentPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FeedCommentPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query by a related FeedItem object
     *
     * @param   FeedItem|PropelObjectCollection $feedItem The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedCommentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFeedItem($feedItem, $comparison = null)
    {
        if ($feedItem instanceof FeedItem) {
            return $this
                ->addUsingAlias(FeedCommentPeer::FEED_ITEM_ID, $feedItem->getId(), $comparison);
        } elseif ($feedItem instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FeedCommentPeer::FEED_ITEM_ID, $feedItem->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByFeedItem() only accepts arguments of type FeedItem or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the FeedItem relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function joinFeedItem($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('FeedItem');

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
            $this->addJoinObject($join, 'FeedItem');
        }

        return $this;
    }

    /**
     * Use the FeedItem relation FeedItem object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Feed\FeedItemQuery A secondary query class using the current class as primary query
     */
    public function useFeedItemQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFeedItem($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'FeedItem', '\Zerebral\BusinessBundle\Model\Feed\FeedItemQuery');
    }

    /**
     * Filter the query by a related FeedContent object
     *
     * @param   FeedContent|PropelObjectCollection $feedContent The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedCommentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFeedContent($feedContent, $comparison = null)
    {
        if ($feedContent instanceof FeedContent) {
            return $this
                ->addUsingAlias(FeedCommentPeer::FEED_CONTENT_ID, $feedContent->getId(), $comparison);
        } elseif ($feedContent instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FeedCommentPeer::FEED_CONTENT_ID, $feedContent->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return FeedCommentQuery The current query, for fluid interface
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
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedCommentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(FeedCommentPeer::CREATED_BY, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FeedCommentPeer::CREATED_BY, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return FeedCommentQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   FeedComment $feedComment Object to remove from the list of results
     *
     * @return FeedCommentQuery The current query, for fluid interface
     */
    public function prune($feedComment = null)
    {
        if ($feedComment) {
            $this->addUsingAlias(FeedCommentPeer::ID, $feedComment->getId(), Criteria::NOT_EQUAL);
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
