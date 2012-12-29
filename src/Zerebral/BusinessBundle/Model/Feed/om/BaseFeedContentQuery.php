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
use Zerebral\BusinessBundle\Model\Feed\FeedContent;
use Zerebral\BusinessBundle\Model\Feed\FeedContentPeer;
use Zerebral\BusinessBundle\Model\Feed\FeedContentQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedItem;

/**
 * @method FeedContentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method FeedContentQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method FeedContentQuery orderByText($order = Criteria::ASC) Order by the text column
 * @method FeedContentQuery orderByLinkUrl($order = Criteria::ASC) Order by the link_url column
 * @method FeedContentQuery orderByLinkTitle($order = Criteria::ASC) Order by the link_title column
 * @method FeedContentQuery orderByLinkDescription($order = Criteria::ASC) Order by the link_description column
 * @method FeedContentQuery orderByLinkThumbnailUrl($order = Criteria::ASC) Order by the link_thumbnail_url column
 *
 * @method FeedContentQuery groupById() Group by the id column
 * @method FeedContentQuery groupByType() Group by the type column
 * @method FeedContentQuery groupByText() Group by the text column
 * @method FeedContentQuery groupByLinkUrl() Group by the link_url column
 * @method FeedContentQuery groupByLinkTitle() Group by the link_title column
 * @method FeedContentQuery groupByLinkDescription() Group by the link_description column
 * @method FeedContentQuery groupByLinkThumbnailUrl() Group by the link_thumbnail_url column
 *
 * @method FeedContentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FeedContentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FeedContentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FeedContentQuery leftJoinFeedItem($relationAlias = null) Adds a LEFT JOIN clause to the query using the FeedItem relation
 * @method FeedContentQuery rightJoinFeedItem($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FeedItem relation
 * @method FeedContentQuery innerJoinFeedItem($relationAlias = null) Adds a INNER JOIN clause to the query using the FeedItem relation
 *
 * @method FeedContentQuery leftJoinFeedComment($relationAlias = null) Adds a LEFT JOIN clause to the query using the FeedComment relation
 * @method FeedContentQuery rightJoinFeedComment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FeedComment relation
 * @method FeedContentQuery innerJoinFeedComment($relationAlias = null) Adds a INNER JOIN clause to the query using the FeedComment relation
 *
 * @method FeedContent findOne(PropelPDO $con = null) Return the first FeedContent matching the query
 * @method FeedContent findOneOrCreate(PropelPDO $con = null) Return the first FeedContent matching the query, or a new FeedContent object populated from the query conditions when no match is found
 *
 * @method FeedContent findOneByType(string $type) Return the first FeedContent filtered by the type column
 * @method FeedContent findOneByText(string $text) Return the first FeedContent filtered by the text column
 * @method FeedContent findOneByLinkUrl(string $link_url) Return the first FeedContent filtered by the link_url column
 * @method FeedContent findOneByLinkTitle(string $link_title) Return the first FeedContent filtered by the link_title column
 * @method FeedContent findOneByLinkDescription(string $link_description) Return the first FeedContent filtered by the link_description column
 * @method FeedContent findOneByLinkThumbnailUrl(string $link_thumbnail_url) Return the first FeedContent filtered by the link_thumbnail_url column
 *
 * @method array findById(int $id) Return FeedContent objects filtered by the id column
 * @method array findByType(string $type) Return FeedContent objects filtered by the type column
 * @method array findByText(string $text) Return FeedContent objects filtered by the text column
 * @method array findByLinkUrl(string $link_url) Return FeedContent objects filtered by the link_url column
 * @method array findByLinkTitle(string $link_title) Return FeedContent objects filtered by the link_title column
 * @method array findByLinkDescription(string $link_description) Return FeedContent objects filtered by the link_description column
 * @method array findByLinkThumbnailUrl(string $link_thumbnail_url) Return FeedContent objects filtered by the link_thumbnail_url column
 */
abstract class BaseFeedContentQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFeedContentQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedContent', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new FeedContentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     FeedContentQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FeedContentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FeedContentQuery) {
            return $criteria;
        }
        $query = new FeedContentQuery();
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
     * @return   FeedContent|FeedContent[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FeedContentPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FeedContentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   FeedContent A model object, or null if the key is not found
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
     * @return   FeedContent A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `type`, `text`, `link_url`, `link_title`, `link_description`, `link_thumbnail_url` FROM `feed_contents` WHERE `id` = :p0';
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
            $obj = new FeedContent();
            $obj->hydrate($row);
            FeedContentPeer::addInstanceToPool($obj, (string) $key);
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
     * @return FeedContent|FeedContent[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FeedContent[]|mixed the list of results, formatted by the current formatter
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
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FeedContentPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FeedContentPeer::ID, $keys, Criteria::IN);
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
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FeedContentPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FeedContentPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the text column
     *
     * Example usage:
     * <code>
     * $query->filterByText('fooValue');   // WHERE text = 'fooValue'
     * $query->filterByText('%fooValue%'); // WHERE text LIKE '%fooValue%'
     * </code>
     *
     * @param     string $text The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterByText($text = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($text)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $text)) {
                $text = str_replace('*', '%', $text);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FeedContentPeer::TEXT, $text, $comparison);
    }

    /**
     * Filter the query on the link_url column
     *
     * Example usage:
     * <code>
     * $query->filterByLinkUrl('fooValue');   // WHERE link_url = 'fooValue'
     * $query->filterByLinkUrl('%fooValue%'); // WHERE link_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $linkUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterByLinkUrl($linkUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($linkUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $linkUrl)) {
                $linkUrl = str_replace('*', '%', $linkUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FeedContentPeer::LINK_URL, $linkUrl, $comparison);
    }

    /**
     * Filter the query on the link_title column
     *
     * Example usage:
     * <code>
     * $query->filterByLinkTitle('fooValue');   // WHERE link_title = 'fooValue'
     * $query->filterByLinkTitle('%fooValue%'); // WHERE link_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $linkTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterByLinkTitle($linkTitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($linkTitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $linkTitle)) {
                $linkTitle = str_replace('*', '%', $linkTitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FeedContentPeer::LINK_TITLE, $linkTitle, $comparison);
    }

    /**
     * Filter the query on the link_description column
     *
     * Example usage:
     * <code>
     * $query->filterByLinkDescription('fooValue');   // WHERE link_description = 'fooValue'
     * $query->filterByLinkDescription('%fooValue%'); // WHERE link_description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $linkDescription The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterByLinkDescription($linkDescription = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($linkDescription)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $linkDescription)) {
                $linkDescription = str_replace('*', '%', $linkDescription);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FeedContentPeer::LINK_DESCRIPTION, $linkDescription, $comparison);
    }

    /**
     * Filter the query on the link_thumbnail_url column
     *
     * Example usage:
     * <code>
     * $query->filterByLinkThumbnailUrl('fooValue');   // WHERE link_thumbnail_url = 'fooValue'
     * $query->filterByLinkThumbnailUrl('%fooValue%'); // WHERE link_thumbnail_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $linkThumbnailUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function filterByLinkThumbnailUrl($linkThumbnailUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($linkThumbnailUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $linkThumbnailUrl)) {
                $linkThumbnailUrl = str_replace('*', '%', $linkThumbnailUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FeedContentPeer::LINK_THUMBNAIL_URL, $linkThumbnailUrl, $comparison);
    }

    /**
     * Filter the query by a related FeedItem object
     *
     * @param   FeedItem|PropelObjectCollection $feedItem  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedContentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFeedItem($feedItem, $comparison = null)
    {
        if ($feedItem instanceof FeedItem) {
            return $this
                ->addUsingAlias(FeedContentPeer::ID, $feedItem->getFeedContentId(), $comparison);
        } elseif ($feedItem instanceof PropelObjectCollection) {
            return $this
                ->useFeedItemQuery()
                ->filterByPrimaryKeys($feedItem->getPrimaryKeys())
                ->endUse();
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
     * @return FeedContentQuery The current query, for fluid interface
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
     * Filter the query by a related FeedComment object
     *
     * @param   FeedComment|PropelObjectCollection $feedComment  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FeedContentQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFeedComment($feedComment, $comparison = null)
    {
        if ($feedComment instanceof FeedComment) {
            return $this
                ->addUsingAlias(FeedContentPeer::ID, $feedComment->getFeedContentId(), $comparison);
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
     * @return FeedContentQuery The current query, for fluid interface
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
     * @param   FeedContent $feedContent Object to remove from the list of results
     *
     * @return FeedContentQuery The current query, for fluid interface
     */
    public function prune($feedContent = null)
    {
        if ($feedContent) {
            $this->addUsingAlias(FeedContentPeer::ID, $feedContent->getId(), Criteria::NOT_EQUAL);
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
