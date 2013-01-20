<?php

namespace Zerebral\BusinessBundle\Model\Assignment\om;

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
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignment;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentFile;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentFilePeer;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentFileQuery;
use Zerebral\BusinessBundle\Model\File\File;

/**
 * @method StudentAssignmentFileQuery orderByfileId($order = Criteria::ASC) Order by the file_id column
 * @method StudentAssignmentFileQuery orderBystudentAssignmentId($order = Criteria::ASC) Order by the student_assignment_id column
 *
 * @method StudentAssignmentFileQuery groupByfileId() Group by the file_id column
 * @method StudentAssignmentFileQuery groupBystudentAssignmentId() Group by the student_assignment_id column
 *
 * @method StudentAssignmentFileQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method StudentAssignmentFileQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method StudentAssignmentFileQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method StudentAssignmentFileQuery leftJoinFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the File relation
 * @method StudentAssignmentFileQuery rightJoinFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the File relation
 * @method StudentAssignmentFileQuery innerJoinFile($relationAlias = null) Adds a INNER JOIN clause to the query using the File relation
 *
 * @method StudentAssignmentFileQuery leftJoinStudentAssignment($relationAlias = null) Adds a LEFT JOIN clause to the query using the StudentAssignment relation
 * @method StudentAssignmentFileQuery rightJoinStudentAssignment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the StudentAssignment relation
 * @method StudentAssignmentFileQuery innerJoinStudentAssignment($relationAlias = null) Adds a INNER JOIN clause to the query using the StudentAssignment relation
 *
 * @method StudentAssignmentFile findOne(PropelPDO $con = null) Return the first StudentAssignmentFile matching the query
 * @method StudentAssignmentFile findOneOrCreate(PropelPDO $con = null) Return the first StudentAssignmentFile matching the query, or a new StudentAssignmentFile object populated from the query conditions when no match is found
 *
 * @method StudentAssignmentFile findOneByfileId(int $file_id) Return the first StudentAssignmentFile filtered by the file_id column
 * @method StudentAssignmentFile findOneBystudentAssignmentId(int $student_assignment_id) Return the first StudentAssignmentFile filtered by the student_assignment_id column
 *
 * @method array findByfileId(int $file_id) Return StudentAssignmentFile objects filtered by the file_id column
 * @method array findBystudentAssignmentId(int $student_assignment_id) Return StudentAssignmentFile objects filtered by the student_assignment_id column
 */
abstract class BaseStudentAssignmentFileQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseStudentAssignmentFileQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignmentFile', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new StudentAssignmentFileQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   StudentAssignmentFileQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return StudentAssignmentFileQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof StudentAssignmentFileQuery) {
            return $criteria;
        }
        $query = new StudentAssignmentFileQuery();
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
                         A Primary key composition: [$file_id, $student_assignment_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   StudentAssignmentFile|StudentAssignmentFile[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = StudentAssignmentFilePeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(StudentAssignmentFilePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 StudentAssignmentFile A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `file_id`, `student_assignment_id` FROM `student_assignment_files` WHERE `file_id` = :p0 AND `student_assignment_id` = :p1';
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
            $obj = new StudentAssignmentFile();
            $obj->hydrate($row);
            StudentAssignmentFilePeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return StudentAssignmentFile|StudentAssignmentFile[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|StudentAssignmentFile[]|mixed the list of results, formatted by the current formatter
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
     * @return StudentAssignmentFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(StudentAssignmentFilePeer::FILE_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(StudentAssignmentFilePeer::STUDENT_ASSIGNMENT_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return StudentAssignmentFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(StudentAssignmentFilePeer::FILE_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(StudentAssignmentFilePeer::STUDENT_ASSIGNMENT_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
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
     * $query->filterByfileId(array('min' => 12)); // WHERE file_id >= 12
     * $query->filterByfileId(array('max' => 12)); // WHERE file_id <= 12
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
     * @return StudentAssignmentFileQuery The current query, for fluid interface
     */
    public function filterByfileId($fileId = null, $comparison = null)
    {
        if (is_array($fileId)) {
            $useMinMax = false;
            if (isset($fileId['min'])) {
                $this->addUsingAlias(StudentAssignmentFilePeer::FILE_ID, $fileId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fileId['max'])) {
                $this->addUsingAlias(StudentAssignmentFilePeer::FILE_ID, $fileId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentAssignmentFilePeer::FILE_ID, $fileId, $comparison);
    }

    /**
     * Filter the query on the student_assignment_id column
     *
     * Example usage:
     * <code>
     * $query->filterBystudentAssignmentId(1234); // WHERE student_assignment_id = 1234
     * $query->filterBystudentAssignmentId(array(12, 34)); // WHERE student_assignment_id IN (12, 34)
     * $query->filterBystudentAssignmentId(array('min' => 12)); // WHERE student_assignment_id >= 12
     * $query->filterBystudentAssignmentId(array('max' => 12)); // WHERE student_assignment_id <= 12
     * </code>
     *
     * @see       filterByStudentAssignment()
     *
     * @param     mixed $studentAssignmentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentAssignmentFileQuery The current query, for fluid interface
     */
    public function filterBystudentAssignmentId($studentAssignmentId = null, $comparison = null)
    {
        if (is_array($studentAssignmentId)) {
            $useMinMax = false;
            if (isset($studentAssignmentId['min'])) {
                $this->addUsingAlias(StudentAssignmentFilePeer::STUDENT_ASSIGNMENT_ID, $studentAssignmentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($studentAssignmentId['max'])) {
                $this->addUsingAlias(StudentAssignmentFilePeer::STUDENT_ASSIGNMENT_ID, $studentAssignmentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentAssignmentFilePeer::STUDENT_ASSIGNMENT_ID, $studentAssignmentId, $comparison);
    }

    /**
     * Filter the query by a related File object
     *
     * @param   File|PropelObjectCollection $file The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 StudentAssignmentFileQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByFile($file, $comparison = null)
    {
        if ($file instanceof File) {
            return $this
                ->addUsingAlias(StudentAssignmentFilePeer::FILE_ID, $file->getId(), $comparison);
        } elseif ($file instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentAssignmentFilePeer::FILE_ID, $file->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return StudentAssignmentFileQuery The current query, for fluid interface
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
     * Filter the query by a related StudentAssignment object
     *
     * @param   StudentAssignment|PropelObjectCollection $studentAssignment The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 StudentAssignmentFileQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByStudentAssignment($studentAssignment, $comparison = null)
    {
        if ($studentAssignment instanceof StudentAssignment) {
            return $this
                ->addUsingAlias(StudentAssignmentFilePeer::STUDENT_ASSIGNMENT_ID, $studentAssignment->getId(), $comparison);
        } elseif ($studentAssignment instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentAssignmentFilePeer::STUDENT_ASSIGNMENT_ID, $studentAssignment->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return StudentAssignmentFileQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   StudentAssignmentFile $studentAssignmentFile Object to remove from the list of results
     *
     * @return StudentAssignmentFileQuery The current query, for fluid interface
     */
    public function prune($studentAssignmentFile = null)
    {
        if ($studentAssignmentFile) {
            $this->addCond('pruneCond0', $this->getAliasedColName(StudentAssignmentFilePeer::FILE_ID), $studentAssignmentFile->getfileId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(StudentAssignmentFilePeer::STUDENT_ASSIGNMENT_ID), $studentAssignmentFile->getstudentAssignmentId(), Criteria::NOT_EQUAL);
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
