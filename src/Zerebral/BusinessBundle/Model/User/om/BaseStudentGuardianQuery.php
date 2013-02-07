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
use Zerebral\BusinessBundle\Model\User\Guardian;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\StudentGuardian;
use Zerebral\BusinessBundle\Model\User\StudentGuardianPeer;
use Zerebral\BusinessBundle\Model\User\StudentGuardianQuery;

/**
 * @method StudentGuardianQuery orderBystudentId($order = Criteria::ASC) Order by the student_id column
 * @method StudentGuardianQuery orderByguardianId($order = Criteria::ASC) Order by the guardian_id column
 *
 * @method StudentGuardianQuery groupBystudentId() Group by the student_id column
 * @method StudentGuardianQuery groupByguardianId() Group by the guardian_id column
 *
 * @method StudentGuardianQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method StudentGuardianQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method StudentGuardianQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method StudentGuardianQuery leftJoinStudent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Student relation
 * @method StudentGuardianQuery rightJoinStudent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Student relation
 * @method StudentGuardianQuery innerJoinStudent($relationAlias = null) Adds a INNER JOIN clause to the query using the Student relation
 *
 * @method StudentGuardianQuery leftJoinGuardian($relationAlias = null) Adds a LEFT JOIN clause to the query using the Guardian relation
 * @method StudentGuardianQuery rightJoinGuardian($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Guardian relation
 * @method StudentGuardianQuery innerJoinGuardian($relationAlias = null) Adds a INNER JOIN clause to the query using the Guardian relation
 *
 * @method StudentGuardian findOne(PropelPDO $con = null) Return the first StudentGuardian matching the query
 * @method StudentGuardian findOneOrCreate(PropelPDO $con = null) Return the first StudentGuardian matching the query, or a new StudentGuardian object populated from the query conditions when no match is found
 *
 * @method StudentGuardian findOneBystudentId(int $student_id) Return the first StudentGuardian filtered by the student_id column
 * @method StudentGuardian findOneByguardianId(int $guardian_id) Return the first StudentGuardian filtered by the guardian_id column
 *
 * @method array findBystudentId(int $student_id) Return StudentGuardian objects filtered by the student_id column
 * @method array findByguardianId(int $guardian_id) Return StudentGuardian objects filtered by the guardian_id column
 */
abstract class BaseStudentGuardianQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseStudentGuardianQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\User\\StudentGuardian', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new StudentGuardianQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   StudentGuardianQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return StudentGuardianQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof StudentGuardianQuery) {
            return $criteria;
        }
        $query = new StudentGuardianQuery();
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
                         A Primary key composition: [$student_id, $guardian_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   StudentGuardian|StudentGuardian[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = StudentGuardianPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(StudentGuardianPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 StudentGuardian A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `student_id`, `guardian_id` FROM `student_guardians` WHERE `student_id` = :p0 AND `guardian_id` = :p1';
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
            $obj = new StudentGuardian();
            $obj->hydrate($row);
            StudentGuardianPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return StudentGuardian|StudentGuardian[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|StudentGuardian[]|mixed the list of results, formatted by the current formatter
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
     * @return StudentGuardianQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(StudentGuardianPeer::STUDENT_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(StudentGuardianPeer::GUARDIAN_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return StudentGuardianQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(StudentGuardianPeer::STUDENT_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(StudentGuardianPeer::GUARDIAN_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the student_id column
     *
     * Example usage:
     * <code>
     * $query->filterBystudentId(1234); // WHERE student_id = 1234
     * $query->filterBystudentId(array(12, 34)); // WHERE student_id IN (12, 34)
     * $query->filterBystudentId(array('min' => 12)); // WHERE student_id >= 12
     * $query->filterBystudentId(array('max' => 12)); // WHERE student_id <= 12
     * </code>
     *
     * @see       filterByStudent()
     *
     * @param     mixed $studentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentGuardianQuery The current query, for fluid interface
     */
    public function filterBystudentId($studentId = null, $comparison = null)
    {
        if (is_array($studentId)) {
            $useMinMax = false;
            if (isset($studentId['min'])) {
                $this->addUsingAlias(StudentGuardianPeer::STUDENT_ID, $studentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($studentId['max'])) {
                $this->addUsingAlias(StudentGuardianPeer::STUDENT_ID, $studentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentGuardianPeer::STUDENT_ID, $studentId, $comparison);
    }

    /**
     * Filter the query on the guardian_id column
     *
     * Example usage:
     * <code>
     * $query->filterByguardianId(1234); // WHERE guardian_id = 1234
     * $query->filterByguardianId(array(12, 34)); // WHERE guardian_id IN (12, 34)
     * $query->filterByguardianId(array('min' => 12)); // WHERE guardian_id >= 12
     * $query->filterByguardianId(array('max' => 12)); // WHERE guardian_id <= 12
     * </code>
     *
     * @see       filterByGuardian()
     *
     * @param     mixed $guardianId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return StudentGuardianQuery The current query, for fluid interface
     */
    public function filterByguardianId($guardianId = null, $comparison = null)
    {
        if (is_array($guardianId)) {
            $useMinMax = false;
            if (isset($guardianId['min'])) {
                $this->addUsingAlias(StudentGuardianPeer::GUARDIAN_ID, $guardianId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($guardianId['max'])) {
                $this->addUsingAlias(StudentGuardianPeer::GUARDIAN_ID, $guardianId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentGuardianPeer::GUARDIAN_ID, $guardianId, $comparison);
    }

    /**
     * Filter the query by a related Student object
     *
     * @param   Student|PropelObjectCollection $student The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 StudentGuardianQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByStudent($student, $comparison = null)
    {
        if ($student instanceof Student) {
            return $this
                ->addUsingAlias(StudentGuardianPeer::STUDENT_ID, $student->getId(), $comparison);
        } elseif ($student instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentGuardianPeer::STUDENT_ID, $student->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByStudent() only accepts arguments of type Student or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Student relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return StudentGuardianQuery The current query, for fluid interface
     */
    public function joinStudent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Student');

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
            $this->addJoinObject($join, 'Student');
        }

        return $this;
    }

    /**
     * Use the Student relation Student object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\StudentQuery A secondary query class using the current class as primary query
     */
    public function useStudentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinStudent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Student', '\Zerebral\BusinessBundle\Model\User\StudentQuery');
    }

    /**
     * Filter the query by a related Guardian object
     *
     * @param   Guardian|PropelObjectCollection $guardian The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 StudentGuardianQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByGuardian($guardian, $comparison = null)
    {
        if ($guardian instanceof Guardian) {
            return $this
                ->addUsingAlias(StudentGuardianPeer::GUARDIAN_ID, $guardian->getId(), $comparison);
        } elseif ($guardian instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentGuardianPeer::GUARDIAN_ID, $guardian->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByGuardian() only accepts arguments of type Guardian or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Guardian relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return StudentGuardianQuery The current query, for fluid interface
     */
    public function joinGuardian($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Guardian');

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
            $this->addJoinObject($join, 'Guardian');
        }

        return $this;
    }

    /**
     * Use the Guardian relation Guardian object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Zerebral\BusinessBundle\Model\User\GuardianQuery A secondary query class using the current class as primary query
     */
    public function useGuardianQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinGuardian($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Guardian', '\Zerebral\BusinessBundle\Model\User\GuardianQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   StudentGuardian $studentGuardian Object to remove from the list of results
     *
     * @return StudentGuardianQuery The current query, for fluid interface
     */
    public function prune($studentGuardian = null)
    {
        if ($studentGuardian) {
            $this->addCond('pruneCond0', $this->getAliasedColName(StudentGuardianPeer::STUDENT_ID), $studentGuardian->getstudentId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(StudentGuardianPeer::GUARDIAN_ID), $studentGuardian->getguardianId(), Criteria::NOT_EQUAL);
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
