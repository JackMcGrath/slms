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
use Zerebral\BusinessBundle\Model\File\FileReferences;
use Zerebral\BusinessBundle\Model\File\FileReferencesPeer;
use Zerebral\BusinessBundle\Model\File\FileReferencesQuery;

/**
 * @method FileReferencesQuery orderByfileId($order = Criteria::ASC) Order by the file_id column
 * @method FileReferencesQuery orderByreferenceId($order = Criteria::ASC) Order by the reference_id column
 * @method FileReferencesQuery orderByreferenceType($order = Criteria::ASC) Order by the reference_type column
 *
 * @method FileReferencesQuery groupByfileId() Group by the file_id column
 * @method FileReferencesQuery groupByreferenceId() Group by the reference_id column
 * @method FileReferencesQuery groupByreferenceType() Group by the reference_type column
 *
 * @method FileReferencesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method FileReferencesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method FileReferencesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method FileReferencesQuery leftJoinFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the File relation
 * @method FileReferencesQuery rightJoinFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the File relation
 * @method FileReferencesQuery innerJoinFile($relationAlias = null) Adds a INNER JOIN clause to the query using the File relation
 *
 * @method FileReferencesQuery leftJoinAssignment($relationAlias = null) Adds a LEFT JOIN clause to the query using the Assignment relation
 * @method FileReferencesQuery rightJoinAssignment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Assignment relation
 * @method FileReferencesQuery innerJoinAssignment($relationAlias = null) Adds a INNER JOIN clause to the query using the Assignment relation
 *
 * @method FileReferences findOne(PropelPDO $con = null) Return the first FileReferences matching the query
 * @method FileReferences findOneOrCreate(PropelPDO $con = null) Return the first FileReferences matching the query, or a new FileReferences object populated from the query conditions when no match is found
 *
 * @method FileReferences findOneByfileId(int $file_id) Return the first FileReferences filtered by the file_id column
 * @method FileReferences findOneByreferenceId(int $reference_id) Return the first FileReferences filtered by the reference_id column
 * @method FileReferences findOneByreferenceType(string $reference_type) Return the first FileReferences filtered by the reference_type column
 *
 * @method array findByfileId(int $file_id) Return FileReferences objects filtered by the file_id column
 * @method array findByreferenceId(int $reference_id) Return FileReferences objects filtered by the reference_id column
 * @method array findByreferenceType(string $reference_type) Return FileReferences objects filtered by the reference_type column
 */
abstract class BaseFileReferencesQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseFileReferencesQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\File\\FileReferences', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new FileReferencesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     FileReferencesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FileReferencesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FileReferencesQuery) {
            return $criteria;
        }
        $query = new FileReferencesQuery();
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
     * $obj = $c->findPk(array(12, 34, 56), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$file_id, $reference_id, $reference_type]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   FileReferences|FileReferences[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FileReferencesPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1], (string) $key[2]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FileReferencesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   FileReferences A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `file_id`, `reference_id`, `reference_type` FROM `file_references` WHERE `file_id` = :p0 AND `reference_id` = :p1 AND `reference_type` = :p2';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->bindValue(':p2', $key[2], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new FileReferences();
            $obj->hydrate($row);
            FileReferencesPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1], (string) $key[2])));
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
     * @return FileReferences|FileReferences[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|FileReferences[]|mixed the list of results, formatted by the current formatter
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
     * @return FileReferencesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(FileReferencesPeer::FILE_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(FileReferencesPeer::REFERENCE_ID, $key[1], Criteria::EQUAL);
        $this->addUsingAlias(FileReferencesPeer::REFERENCE_TYPE, $key[2], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FileReferencesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(FileReferencesPeer::FILE_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(FileReferencesPeer::REFERENCE_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $cton2 = $this->getNewCriterion(FileReferencesPeer::REFERENCE_TYPE, $key[2], Criteria::EQUAL);
            $cton0->addAnd($cton2);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the file_id column
     *
     * Example usage:
     * <code>
     * $query->filterByfileId(1234); // WHERE file_id = 1234
     * $query->filterByfileId(array(12, 34)); // WHERE file_id IN (12, 34)
     * $query->filterByfileId(array('min' => 12)); // WHERE file_id > 12
     * </code>
     *
     * @see       filterByFile()
     *
     * @param     mixed $fileId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FileReferencesQuery The current query, for fluid interface
     */
    public function filterByfileId($fileId = null, $comparison = null)
    {
        if (is_array($fileId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FileReferencesPeer::FILE_ID, $fileId, $comparison);
    }

    /**
     * Filter the query on the reference_id column
     *
     * Example usage:
     * <code>
     * $query->filterByreferenceId(1234); // WHERE reference_id = 1234
     * $query->filterByreferenceId(array(12, 34)); // WHERE reference_id IN (12, 34)
     * $query->filterByreferenceId(array('min' => 12)); // WHERE reference_id > 12
     * </code>
     *
     * @see       filterByAssignment()
     *
     * @param     mixed $referenceId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FileReferencesQuery The current query, for fluid interface
     */
    public function filterByreferenceId($referenceId = null, $comparison = null)
    {
        if (is_array($referenceId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FileReferencesPeer::REFERENCE_ID, $referenceId, $comparison);
    }

    /**
     * Filter the query on the reference_type column
     *
     * Example usage:
     * <code>
     * $query->filterByreferenceType('fooValue');   // WHERE reference_type = 'fooValue'
     * $query->filterByreferenceType('%fooValue%'); // WHERE reference_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $referenceType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FileReferencesQuery The current query, for fluid interface
     */
    public function filterByreferenceType($referenceType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($referenceType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $referenceType)) {
                $referenceType = str_replace('*', '%', $referenceType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FileReferencesPeer::REFERENCE_TYPE, $referenceType, $comparison);
    }

    /**
     * Filter the query by a related File object
     *
     * @param   File|PropelObjectCollection $file The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FileReferencesQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByFile($file, $comparison = null)
    {
        if ($file instanceof File) {
            return $this
                ->addUsingAlias(FileReferencesPeer::FILE_ID, $file->getId(), $comparison);
        } elseif ($file instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FileReferencesPeer::FILE_ID, $file->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByFile() only accepts arguments of type File or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the File relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return FileReferencesQuery The current query, for fluid interface
     */
    public function joinFile($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('File');

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
            $this->addJoinObject($join, 'File');
        }

        return $this;
    }

    /**
     * Use the File relation File object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\File\FileQuery A secondary query class using the current class as primary query
     */
    public function useFileQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'File', '\Zerebral\BusinessBundle\Model\File\FileQuery');
    }

    /**
     * Filter the query by a related Assignment object
     *
     * @param   Assignment|PropelObjectCollection $assignment The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   FileReferencesQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByAssignment($assignment, $comparison = null)
    {
        if ($assignment instanceof Assignment) {
            return $this
                ->addUsingAlias(FileReferencesPeer::REFERENCE_ID, $assignment->getId(), $comparison);
        } elseif ($assignment instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FileReferencesPeer::REFERENCE_ID, $assignment->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return FileReferencesQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   FileReferences $fileReferences Object to remove from the list of results
     *
     * @return FileReferencesQuery The current query, for fluid interface
     */
    public function prune($fileReferences = null)
    {
        if ($fileReferences) {
            $this->addCond('pruneCond0', $this->getAliasedColName(FileReferencesPeer::FILE_ID), $fileReferences->getfileId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(FileReferencesPeer::REFERENCE_ID), $fileReferences->getreferenceId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond2', $this->getAliasedColName(FileReferencesPeer::REFERENCE_TYPE), $fileReferences->getreferenceType(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1', 'pruneCond2'), Criteria::LOGICAL_OR);
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
