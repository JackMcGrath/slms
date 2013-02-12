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
use Zerebral\BusinessBundle\Model\User\GuardianInvite;
use Zerebral\BusinessBundle\Model\User\GuardianInvitePeer;
use Zerebral\BusinessBundle\Model\User\GuardianInviteQuery;
use Zerebral\BusinessBundle\Model\User\Student;

/**
 * @method GuardianInviteQuery orderByStudentId($order = Criteria::ASC) Order by the student_id column
 * @method GuardianInviteQuery orderByGuardianEmail($order = Criteria::ASC) Order by the guardian_email column
 * @method GuardianInviteQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method GuardianInviteQuery orderByActivated($order = Criteria::ASC) Order by the activated column
 *
 * @method GuardianInviteQuery groupByStudentId() Group by the student_id column
 * @method GuardianInviteQuery groupByGuardianEmail() Group by the guardian_email column
 * @method GuardianInviteQuery groupByCode() Group by the code column
 * @method GuardianInviteQuery groupByActivated() Group by the activated column
 *
 * @method GuardianInviteQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method GuardianInviteQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method GuardianInviteQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method GuardianInviteQuery leftJoinStudent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Student relation
 * @method GuardianInviteQuery rightJoinStudent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Student relation
 * @method GuardianInviteQuery innerJoinStudent($relationAlias = null) Adds a INNER JOIN clause to the query using the Student relation
 *
 * @method GuardianInvite findOne(PropelPDO $con = null) Return the first GuardianInvite matching the query
 * @method GuardianInvite findOneOrCreate(PropelPDO $con = null) Return the first GuardianInvite matching the query, or a new GuardianInvite object populated from the query conditions when no match is found
 *
 * @method GuardianInvite findOneByStudentId(int $student_id) Return the first GuardianInvite filtered by the student_id column
 * @method GuardianInvite findOneByGuardianEmail(string $guardian_email) Return the first GuardianInvite filtered by the guardian_email column
 * @method GuardianInvite findOneByActivated(boolean $activated) Return the first GuardianInvite filtered by the activated column
 *
 * @method array findByStudentId(int $student_id) Return GuardianInvite objects filtered by the student_id column
 * @method array findByGuardianEmail(string $guardian_email) Return GuardianInvite objects filtered by the guardian_email column
 * @method array findByCode(string $code) Return GuardianInvite objects filtered by the code column
 * @method array findByActivated(boolean $activated) Return GuardianInvite objects filtered by the activated column
 */
abstract class BaseGuardianInviteQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseGuardianInviteQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = 'Zerebral\\BusinessBundle\\Model\\User\\GuardianInvite', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
        EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent($this));
}

    /**
     * Returns a new GuardianInviteQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     GuardianInviteQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return GuardianInviteQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof GuardianInviteQuery) {
            return $criteria;
        }
        $query = new GuardianInviteQuery();
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
     * @return   GuardianInvite|GuardianInvite[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = GuardianInvitePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(GuardianInvitePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   GuardianInvite A model object, or null if the key is not found
     * @throws   PropelException
     */
     public function findOneByCode($key, $con = null)
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
     * @return   GuardianInvite A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `student_id`, `guardian_email`, `code`, `activated` FROM `guardian_invites` WHERE `code` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new GuardianInvite();
            $obj->hydrate($row);
            GuardianInvitePeer::addInstanceToPool($obj, (string) $key);
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
     * @return GuardianInvite|GuardianInvite[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|GuardianInvite[]|mixed the list of results, formatted by the current formatter
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
     * @return GuardianInviteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(GuardianInvitePeer::CODE, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return GuardianInviteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(GuardianInvitePeer::CODE, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the student_id column
     *
     * Example usage:
     * <code>
     * $query->filterByStudentId(1234); // WHERE student_id = 1234
     * $query->filterByStudentId(array(12, 34)); // WHERE student_id IN (12, 34)
     * $query->filterByStudentId(array('min' => 12)); // WHERE student_id > 12
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
     * @return GuardianInviteQuery The current query, for fluid interface
     */
    public function filterByStudentId($studentId = null, $comparison = null)
    {
        if (is_array($studentId)) {
            $useMinMax = false;
            if (isset($studentId['min'])) {
                $this->addUsingAlias(GuardianInvitePeer::STUDENT_ID, $studentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($studentId['max'])) {
                $this->addUsingAlias(GuardianInvitePeer::STUDENT_ID, $studentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GuardianInvitePeer::STUDENT_ID, $studentId, $comparison);
    }

    /**
     * Filter the query on the guardian_email column
     *
     * Example usage:
     * <code>
     * $query->filterByGuardianEmail('fooValue');   // WHERE guardian_email = 'fooValue'
     * $query->filterByGuardianEmail('%fooValue%'); // WHERE guardian_email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $guardianEmail The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GuardianInviteQuery The current query, for fluid interface
     */
    public function filterByGuardianEmail($guardianEmail = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($guardianEmail)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $guardianEmail)) {
                $guardianEmail = str_replace('*', '%', $guardianEmail);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GuardianInvitePeer::GUARDIAN_EMAIL, $guardianEmail, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GuardianInviteQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GuardianInvitePeer::CODE, $code, $comparison);
    }

    /**
     * Filter the query on the activated column
     *
     * Example usage:
     * <code>
     * $query->filterByActivated(true); // WHERE activated = true
     * $query->filterByActivated('yes'); // WHERE activated = true
     * </code>
     *
     * @param     boolean|string $activated The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GuardianInviteQuery The current query, for fluid interface
     */
    public function filterByActivated($activated = null, $comparison = null)
    {
        if (is_string($activated)) {
            $activated = in_array(strtolower($activated), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(GuardianInvitePeer::ACTIVATED, $activated, $comparison);
    }

    /**
     * Filter the query by a related Student object
     *
     * @param   Student|PropelObjectCollection $student The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   GuardianInviteQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByStudent($student, $comparison = null)
    {
        if ($student instanceof Student) {
            return $this
                ->addUsingAlias(GuardianInvitePeer::STUDENT_ID, $student->getId(), $comparison);
        } elseif ($student instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GuardianInvitePeer::STUDENT_ID, $student->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return GuardianInviteQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   GuardianInvite $guardianInvite Object to remove from the list of results
     *
     * @return GuardianInviteQuery The current query, for fluid interface
     */
    public function prune($guardianInvite = null)
    {
        if ($guardianInvite) {
            $this->addUsingAlias(GuardianInvitePeer::CODE, $guardianInvite->getCode(), Criteria::NOT_EQUAL);
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
