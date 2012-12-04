<?php

namespace Zerebral\BusinessBundle\Model\Document\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \PDO;
use \Propel;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Glorpen\PropelEvent\PropelEventBundle\Dispatcher\EventDispatcherProxy;
use Glorpen\PropelEvent\PropelEventBundle\Events\QueryEvent;
use Zerebral\BusinessBundle\Model\Document\Document;
use Zerebral\BusinessBundle\Model\Document\DocumentPeer;
use Zerebral\BusinessBundle\Model\Document\DocumentQuery;

/**
 * @method DocumentQuery orderByReferenceId($order = Criteria::ASC) Order by the reference_id column
 * @method DocumentQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method DocumentQuery orderByStorage($order = Criteria::ASC) Order by the storage column
 * @method DocumentQuery orderByPath($order = Criteria::ASC) Order by the path column
 * @method DocumentQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 *
 * @method DocumentQuery groupByReferenceId() Group by the reference_id column
 * @method DocumentQuery groupByType() Group by the type column
 * @method DocumentQuery groupByStorage() Group by the storage column
 * @method DocumentQuery groupByPath() Group by the path column
 * @method DocumentQuery groupByCreatedAt() Group by the created_at column
 *
 * @method DocumentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method DocumentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method DocumentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method Document findOne(PropelPDO $con = null) Return the first Document matching the query
 * @method Document findOneOrCreate(PropelPDO $con = null) Return the first Document matching the query, or a new Document object populated from the query conditions when no match is found
 *
 * @method Document findOneByReferenceId(int $reference_id) Return the first Document filtered by the reference_id column
 * @method Document findOneByType(string $type) Return the first Document filtered by the type column
 * @method Document findOneByStorage(string $storage) Return the first Document filtered by the storage column
 * @method Document findOneByPath(string $path) Return the first Document filtered by the path column
 * @method Document findOneByCreatedAt(string $created_at) Return the first Document filtered by the created_at column
 *
 * @method array findByReferenceId(int $reference_id) Return Document objects filtered by the reference_id column
 * @method array findByType(string $type) Return Document objects filtered by the type column
 * @method array findByStorage(string $storage) Return Document objects filtered by the storage column
 * @method array findByPath(string $path) Return Document objects filtered by the path column
 * @method array findByCreatedAt(string $created_at) Return Document objects filtered by the created_at column
 */
abstract class BaseDocumentQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseDocumentQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Document\\Document', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new DocumentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     DocumentQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return DocumentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof DocumentQuery) {
            return $criteria;
        }
        $query = new DocumentQuery();
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
                         A Primary key composition: [$reference_id, $type]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Document|Document[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DocumentPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(DocumentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Document A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `reference_id`, `type`, `storage`, `path`, `created_at` FROM `documents` WHERE `reference_id` = :p0 AND `type` = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Document();
            $obj->hydrate($row);
            DocumentPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return Document|Document[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Document[]|mixed the list of results, formatted by the current formatter
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
     * @return DocumentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(DocumentPeer::REFERENCE_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(DocumentPeer::TYPE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return DocumentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(DocumentPeer::REFERENCE_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(DocumentPeer::TYPE, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the reference_id column
     *
     * Example usage:
     * <code>
     * $query->filterByReferenceId(1234); // WHERE reference_id = 1234
     * $query->filterByReferenceId(array(12, 34)); // WHERE reference_id IN (12, 34)
     * $query->filterByReferenceId(array('min' => 12)); // WHERE reference_id > 12
     * </code>
     *
     * @param     mixed $referenceId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DocumentQuery The current query, for fluid interface
     */
    public function filterByReferenceId($referenceId = null, $comparison = null)
    {
        if (is_array($referenceId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(DocumentPeer::REFERENCE_ID, $referenceId, $comparison);
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
     * @return DocumentQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DocumentPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the storage column
     *
     * Example usage:
     * <code>
     * $query->filterByStorage('fooValue');   // WHERE storage = 'fooValue'
     * $query->filterByStorage('%fooValue%'); // WHERE storage LIKE '%fooValue%'
     * </code>
     *
     * @param     string $storage The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DocumentQuery The current query, for fluid interface
     */
    public function filterByStorage($storage = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($storage)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $storage)) {
                $storage = str_replace('*', '%', $storage);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DocumentPeer::STORAGE, $storage, $comparison);
    }

    /**
     * Filter the query on the path column
     *
     * Example usage:
     * <code>
     * $query->filterByPath('fooValue');   // WHERE path = 'fooValue'
     * $query->filterByPath('%fooValue%'); // WHERE path LIKE '%fooValue%'
     * </code>
     *
     * @param     string $path The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return DocumentQuery The current query, for fluid interface
     */
    public function filterByPath($path = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($path)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $path)) {
                $path = str_replace('*', '%', $path);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DocumentPeer::PATH, $path, $comparison);
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
     * @return DocumentQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DocumentPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DocumentPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DocumentPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Document $document Object to remove from the list of results
     *
     * @return DocumentQuery The current query, for fluid interface
     */
    public function prune($document = null)
    {
        if ($document) {
            $this->addCond('pruneCond0', $this->getAliasedColName(DocumentPeer::REFERENCE_ID), $document->getReferenceId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(DocumentPeer::TYPE), $document->getType(), Criteria::NOT_EQUAL);
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
