<?php

namespace Zerebral\BusinessBundle\Model\Message\om;

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
use Zerebral\BusinessBundle\Model\File\File;
use Zerebral\BusinessBundle\Model\Message\Message;
use Zerebral\BusinessBundle\Model\Message\MessageFile;
use Zerebral\BusinessBundle\Model\Message\MessagePeer;
use Zerebral\BusinessBundle\Model\Message\MessageQuery;
use Zerebral\BusinessBundle\Model\User\User;

/**
 * @method MessageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method MessageQuery orderByThreadId($order = Criteria::ASC) Order by the thread_id column
 * @method MessageQuery orderByFromId($order = Criteria::ASC) Order by the from_id column
 * @method MessageQuery orderByToId($order = Criteria::ASC) Order by the to_id column
 * @method MessageQuery orderByIsRead($order = Criteria::ASC) Order by the is_read column
 * @method MessageQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method MessageQuery orderBySubject($order = Criteria::ASC) Order by the subject column
 * @method MessageQuery orderByBody($order = Criteria::ASC) Order by the body column
 * @method MessageQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 *
 * @method MessageQuery groupById() Group by the id column
 * @method MessageQuery groupByThreadId() Group by the thread_id column
 * @method MessageQuery groupByFromId() Group by the from_id column
 * @method MessageQuery groupByToId() Group by the to_id column
 * @method MessageQuery groupByIsRead() Group by the is_read column
 * @method MessageQuery groupByUserId() Group by the user_id column
 * @method MessageQuery groupBySubject() Group by the subject column
 * @method MessageQuery groupByBody() Group by the body column
 * @method MessageQuery groupByCreatedAt() Group by the created_at column
 *
 * @method MessageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method MessageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method MessageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method MessageQuery leftJoinUserRelatedByUserId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserRelatedByUserId relation
 * @method MessageQuery rightJoinUserRelatedByUserId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserRelatedByUserId relation
 * @method MessageQuery innerJoinUserRelatedByUserId($relationAlias = null) Adds a INNER JOIN clause to the query using the UserRelatedByUserId relation
 *
 * @method MessageQuery leftJoinUserRelatedByFromId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserRelatedByFromId relation
 * @method MessageQuery rightJoinUserRelatedByFromId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserRelatedByFromId relation
 * @method MessageQuery innerJoinUserRelatedByFromId($relationAlias = null) Adds a INNER JOIN clause to the query using the UserRelatedByFromId relation
 *
 * @method MessageQuery leftJoinUserRelatedByToId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserRelatedByToId relation
 * @method MessageQuery rightJoinUserRelatedByToId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserRelatedByToId relation
 * @method MessageQuery innerJoinUserRelatedByToId($relationAlias = null) Adds a INNER JOIN clause to the query using the UserRelatedByToId relation
 *
 * @method MessageQuery leftJoinMessageFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the MessageFile relation
 * @method MessageQuery rightJoinMessageFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MessageFile relation
 * @method MessageQuery innerJoinMessageFile($relationAlias = null) Adds a INNER JOIN clause to the query using the MessageFile relation
 *
 * @method Message findOne(PropelPDO $con = null) Return the first Message matching the query
 * @method Message findOneOrCreate(PropelPDO $con = null) Return the first Message matching the query, or a new Message object populated from the query conditions when no match is found
 *
 * @method Message findOneByThreadId(string $thread_id) Return the first Message filtered by the thread_id column
 * @method Message findOneByFromId(int $from_id) Return the first Message filtered by the from_id column
 * @method Message findOneByToId(int $to_id) Return the first Message filtered by the to_id column
 * @method Message findOneByIsRead(boolean $is_read) Return the first Message filtered by the is_read column
 * @method Message findOneByUserId(int $user_id) Return the first Message filtered by the user_id column
 * @method Message findOneBySubject(string $subject) Return the first Message filtered by the subject column
 * @method Message findOneByBody(string $body) Return the first Message filtered by the body column
 * @method Message findOneByCreatedAt(string $created_at) Return the first Message filtered by the created_at column
 *
 * @method array findById(int $id) Return Message objects filtered by the id column
 * @method array findByThreadId(string $thread_id) Return Message objects filtered by the thread_id column
 * @method array findByFromId(int $from_id) Return Message objects filtered by the from_id column
 * @method array findByToId(int $to_id) Return Message objects filtered by the to_id column
 * @method array findByIsRead(boolean $is_read) Return Message objects filtered by the is_read column
 * @method array findByUserId(int $user_id) Return Message objects filtered by the user_id column
 * @method array findBySubject(string $subject) Return Message objects filtered by the subject column
 * @method array findByBody(string $body) Return Message objects filtered by the body column
 * @method array findByCreatedAt(string $created_at) Return Message objects filtered by the created_at column
 */
abstract class BaseMessageQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseMessageQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Message\\Message', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new MessageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     MessageQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return MessageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof MessageQuery) {
            return $criteria;
        }
        $query = new MessageQuery();
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
     * @return   Message|Message[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = MessagePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(MessagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Message A model object, or null if the key is not found
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
     * @return   Message A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `thread_id`, `from_id`, `to_id`, `is_read`, `user_id`, `subject`, `body`, `created_at` FROM `messages` WHERE `id` = :p0';
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
            $obj = new Message();
            $obj->hydrate($row);
            MessagePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Message|Message[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Message[]|mixed the list of results, formatted by the current formatter
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
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(MessagePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(MessagePeer::ID, $keys, Criteria::IN);
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
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(MessagePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the thread_id column
     *
     * Example usage:
     * <code>
     * $query->filterByThreadId(1234); // WHERE thread_id = 1234
     * $query->filterByThreadId(array(12, 34)); // WHERE thread_id IN (12, 34)
     * $query->filterByThreadId(array('min' => 12)); // WHERE thread_id > 12
     * </code>
     *
     * @param     mixed $threadId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByThreadId($threadId = null, $comparison = null)
    {
        if (is_array($threadId)) {
            $useMinMax = false;
            if (isset($threadId['min'])) {
                $this->addUsingAlias(MessagePeer::THREAD_ID, $threadId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($threadId['max'])) {
                $this->addUsingAlias(MessagePeer::THREAD_ID, $threadId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagePeer::THREAD_ID, $threadId, $comparison);
    }

    /**
     * Filter the query on the from_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFromId(1234); // WHERE from_id = 1234
     * $query->filterByFromId(array(12, 34)); // WHERE from_id IN (12, 34)
     * $query->filterByFromId(array('min' => 12)); // WHERE from_id > 12
     * </code>
     *
     * @see       filterByUserRelatedByFromId()
     *
     * @param     mixed $fromId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByFromId($fromId = null, $comparison = null)
    {
        if (is_array($fromId)) {
            $useMinMax = false;
            if (isset($fromId['min'])) {
                $this->addUsingAlias(MessagePeer::FROM_ID, $fromId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fromId['max'])) {
                $this->addUsingAlias(MessagePeer::FROM_ID, $fromId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagePeer::FROM_ID, $fromId, $comparison);
    }

    /**
     * Filter the query on the to_id column
     *
     * Example usage:
     * <code>
     * $query->filterByToId(1234); // WHERE to_id = 1234
     * $query->filterByToId(array(12, 34)); // WHERE to_id IN (12, 34)
     * $query->filterByToId(array('min' => 12)); // WHERE to_id > 12
     * </code>
     *
     * @see       filterByUserRelatedByToId()
     *
     * @param     mixed $toId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByToId($toId = null, $comparison = null)
    {
        if (is_array($toId)) {
            $useMinMax = false;
            if (isset($toId['min'])) {
                $this->addUsingAlias(MessagePeer::TO_ID, $toId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($toId['max'])) {
                $this->addUsingAlias(MessagePeer::TO_ID, $toId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagePeer::TO_ID, $toId, $comparison);
    }

    /**
     * Filter the query on the is_read column
     *
     * Example usage:
     * <code>
     * $query->filterByIsRead(true); // WHERE is_read = true
     * $query->filterByIsRead('yes'); // WHERE is_read = true
     * </code>
     *
     * @param     boolean|string $isRead The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByIsRead($isRead = null, $comparison = null)
    {
        if (is_string($isRead)) {
            $isRead = in_array(strtolower($isRead), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(MessagePeer::IS_READ, $isRead, $comparison);
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
     * @see       filterByUserRelatedByUserId()
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(MessagePeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(MessagePeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagePeer::USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the subject column
     *
     * Example usage:
     * <code>
     * $query->filterBySubject('fooValue');   // WHERE subject = 'fooValue'
     * $query->filterBySubject('%fooValue%'); // WHERE subject LIKE '%fooValue%'
     * </code>
     *
     * @param     string $subject The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterBySubject($subject = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($subject)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $subject)) {
                $subject = str_replace('*', '%', $subject);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MessagePeer::SUBJECT, $subject, $comparison);
    }

    /**
     * Filter the query on the body column
     *
     * Example usage:
     * <code>
     * $query->filterByBody('fooValue');   // WHERE body = 'fooValue'
     * $query->filterByBody('%fooValue%'); // WHERE body LIKE '%fooValue%'
     * </code>
     *
     * @param     string $body The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByBody($body = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($body)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $body)) {
                $body = str_replace('*', '%', $body);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MessagePeer::BODY, $body, $comparison);
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
     * @return MessageQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(MessagePeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(MessagePeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MessagePeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   MessageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUserRelatedByUserId($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(MessagePeer::USER_ID, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MessagePeer::USER_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUserRelatedByUserId() only accepts arguments of type User or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserRelatedByUserId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function joinUserRelatedByUserId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserRelatedByUserId');

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
            $this->addJoinObject($join, 'UserRelatedByUserId');
        }

        return $this;
    }

    /**
     * Use the UserRelatedByUserId relation User object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\UserQuery A secondary query class using the current class as primary query
     */
    public function useUserRelatedByUserIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserRelatedByUserId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserRelatedByUserId', '\Zerebral\BusinessBundle\Model\User\UserQuery');
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   MessageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUserRelatedByFromId($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(MessagePeer::FROM_ID, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MessagePeer::FROM_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUserRelatedByFromId() only accepts arguments of type User or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserRelatedByFromId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function joinUserRelatedByFromId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserRelatedByFromId');

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
            $this->addJoinObject($join, 'UserRelatedByFromId');
        }

        return $this;
    }

    /**
     * Use the UserRelatedByFromId relation User object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\UserQuery A secondary query class using the current class as primary query
     */
    public function useUserRelatedByFromIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserRelatedByFromId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserRelatedByFromId', '\Zerebral\BusinessBundle\Model\User\UserQuery');
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   MessageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUserRelatedByToId($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(MessagePeer::TO_ID, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MessagePeer::TO_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUserRelatedByToId() only accepts arguments of type User or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserRelatedByToId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function joinUserRelatedByToId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserRelatedByToId');

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
            $this->addJoinObject($join, 'UserRelatedByToId');
        }

        return $this;
    }

    /**
     * Use the UserRelatedByToId relation User object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\UserQuery A secondary query class using the current class as primary query
     */
    public function useUserRelatedByToIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserRelatedByToId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserRelatedByToId', '\Zerebral\BusinessBundle\Model\User\UserQuery');
    }

    /**
     * Filter the query by a related MessageFile object
     *
     * @param   MessageFile|PropelObjectCollection $messageFile  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   MessageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByMessageFile($messageFile, $comparison = null)
    {
        if ($messageFile instanceof MessageFile) {
            return $this
                ->addUsingAlias(MessagePeer::ID, $messageFile->getmessageId(), $comparison);
        } elseif ($messageFile instanceof PropelObjectCollection) {
            return $this
                ->useMessageFileQuery()
                ->filterByPrimaryKeys($messageFile->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMessageFile() only accepts arguments of type MessageFile or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MessageFile relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function joinMessageFile($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MessageFile');

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
            $this->addJoinObject($join, 'MessageFile');
        }

        return $this;
    }

    /**
     * Use the MessageFile relation MessageFile object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\Message\MessageFileQuery A secondary query class using the current class as primary query
     */
    public function useMessageFileQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMessageFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MessageFile', '\Zerebral\BusinessBundle\Model\Message\MessageFileQuery');
    }

    /**
     * Filter the query by a related File object
     * using the message_files table as cross reference
     *
     * @param   File $file the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   MessageQuery The current query, for fluid interface
     */
    public function filterByFile($file, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useMessageFileQuery()
            ->filterByFile($file, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Message $message Object to remove from the list of results
     *
     * @return MessageQuery The current query, for fluid interface
     */
    public function prune($message = null)
    {
        if ($message) {
            $this->addUsingAlias(MessagePeer::ID, $message->getId(), Criteria::NOT_EQUAL);
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
