<?php

namespace Zerebral\BusinessBundle\Model\File\om;

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
use Zerebral\BusinessBundle\Model\File\File;
use Zerebral\BusinessBundle\Model\File\FilePeer;
use Zerebral\BusinessBundle\Model\File\FileQuery;
use Zerebral\BusinessBundle\Model\File\FileReferences;
use Zerebral\BusinessBundle\Model\User\Student;

/**
 * @method FileQuery orderById($order = Criteria::ASC) Order by the id column
 * @method FileQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method FileQuery orderBySize($order = Criteria::ASC) Order by the size column
 * @method FileQuery orderByMimeType($order = Criteria::ASC) Order by the mime_type column
 * @method FileQuery orderByStorage($order = Criteria::ASC) Order by the storage column
 * @method FileQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 *
 * @method FileQuery groupById() Group by the id column
 * @method FileQuery groupByName() Group by the name column
 * @method FileQuery groupBySize() Group by the size column
 * @method FileQuery groupByMimeType() Group by the mime_type column
 * @method FileQuery groupByStorage() Group by the storage column
 * @method FileQuery groupByCreatedAt() Group by the created_at column
 *
 * @method FileQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FileQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FileQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FileQuery leftJoinFileReferences($relationAlias = null) Adds a LEFT JOIN clause to the query using the FileReferences relation
 * @method FileQuery rightJoinFileReferences($relationAlias = null) Adds a RIGHT JOIN clause to the query using the FileReferences relation
 * @method FileQuery innerJoinFileReferences($relationAlias = null) Adds a INNER JOIN clause to the query using the FileReferences relation
 *
 * @method File findOne(PropelPDO $con = null) Return the first File matching the query
 * @method File findOneOrCreate(PropelPDO $con = null) Return the first File matching the query, or a new File object populated from the query conditions when no match is found
 *
 * @method File findOneByName(string $name) Return the first File filtered by the name column
 * @method File findOneBySize(int $size) Return the first File filtered by the size column
 * @method File findOneByMimeType(string $mime_type) Return the first File filtered by the mime_type column
 * @method File findOneByStorage(string $storage) Return the first File filtered by the storage column
 * @method File findOneByCreatedAt(string $created_at) Return the first File filtered by the created_at column
 *
 * @method array findById(int $id) Return File objects filtered by the id column
 * @method array findByName(string $name) Return File objects filtered by the name column
 * @method array findBySize(int $size) Return File objects filtered by the size column
 * @method array findByMimeType(string $mime_type) Return File objects filtered by the mime_type column
 * @method array findByStorage(string $storage) Return File objects filtered by the storage column
 * @method array findByCreatedAt(string $created_at) Return File objects filtered by the created_at column
 */
abstract class BaseFileQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFileQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\File\\File', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new FileQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     FileQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FileQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FileQuery) {
            return $criteria;
        }
        $query = new FileQuery();
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
     * @return   File|File[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FilePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   File A model object, or null if the key is not found
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
     * @return   File A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `name`, `size`, `mime_type`, `storage`, `created_at` FROM `files` WHERE `id` = :p0';
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
            $obj = new File();
            $obj->hydrate($row);
            FilePeer::addInstanceToPool($obj, (string) $key);
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
     * @return File|File[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|File[]|mixed the list of results, formatted by the current formatter
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
     * @return FileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FilePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FilePeer::ID, $keys, Criteria::IN);
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
     * @return FileQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FilePeer::ID, $id, $comparison);
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
     * @return FileQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FilePeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the size column
     *
     * Example usage:
     * <code>
     * $query->filterBySize(1234); // WHERE size = 1234
     * $query->filterBySize(array(12, 34)); // WHERE size IN (12, 34)
     * $query->filterBySize(array('min' => 12)); // WHERE size > 12
     * </code>
     *
     * @param     mixed $size The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FileQuery The current query, for fluid interface
     */
    public function filterBySize($size = null, $comparison = null)
    {
        if (is_array($size)) {
            $useMinMax = false;
            if (isset($size['min'])) {
                $this->addUsingAlias(FilePeer::SIZE, $size['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($size['max'])) {
                $this->addUsingAlias(FilePeer::SIZE, $size['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FilePeer::SIZE, $size, $comparison);
    }

    /**
     * Filter the query on the mime_type column
     *
     * Example usage:
     * <code>
     * $query->filterByMimeType('fooValue');   // WHERE mime_type = 'fooValue'
     * $query->filterByMimeType('%fooValue%'); // WHERE mime_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $mimeType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FileQuery The current query, for fluid interface
     */
    public function filterByMimeType($mimeType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($mimeType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $mimeType)) {
                $mimeType = str_replace('*', '%', $mimeType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FilePeer::MIME_TYPE, $mimeType, $comparison);
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
     * @return FileQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FilePeer::STORAGE, $storage, $comparison);
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
     * @return FileQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(FilePeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(FilePeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FilePeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query by a related FileReferences object
     *
     * @param   FileReferences|PropelObjectCollection $fileReferences  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FileQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFileReferences($fileReferences, $comparison = null)
    {
        if ($fileReferences instanceof FileReferences) {
            return $this
                ->addUsingAlias(FilePeer::ID, $fileReferences->getfileId(), $comparison);
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
     * @return FileQuery The current query, for fluid interface
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
     * Filter the query by a related Assignment object
     * using the file_references table as cross reference
     *
     * @param   Assignment $assignment the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FileQuery The current query, for fluid interface
     */
    public function filterByassignmentReferenceId($assignment, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useFileReferencesQuery()
            ->filterByassignmentReferenceId($assignment, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Student object
     * using the file_references table as cross reference
     *
     * @param   Student $student the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FileQuery The current query, for fluid interface
     */
    public function filterBystudentsReferenceId($student, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useFileReferencesQuery()
            ->filterBystudentsReferenceId($student, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   File $file Object to remove from the list of results
     *
     * @return FileQuery The current query, for fluid interface
     */
    public function prune($file = null)
    {
        if ($file) {
            $this->addUsingAlias(FilePeer::ID, $file->getId(), Criteria::NOT_EQUAL);
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
