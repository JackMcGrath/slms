<?php

namespace Zerebral\BusinessBundle\Model\User\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'students' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.User.map
 */
class StudentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.User.map.StudentTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('students');
        $this->setPhpName('Student');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\User\\Student');
        $this->setPackage('Zerebral.BusinessBundle.Model.User');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addColumn('bio', 'Bio', 'VARCHAR', false, 160, null);
        $this->addColumn('activities', 'Activities', 'LONGVARCHAR', false, null, null);
        $this->addColumn('interests', 'Interests', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), null, null);
        $this->addRelation('StudentAssignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignment', RelationMap::ONE_TO_MANY, array('id' => 'student_id', ), 'CASCADE', 'CASCADE', 'StudentAssignments');
        $this->addRelation('StudentAttendance', 'Zerebral\\BusinessBundle\\Model\\Attendance\\StudentAttendance', RelationMap::ONE_TO_MANY, array('id' => 'student_id', ), 'CASCADE', 'CASCADE', 'StudentAttendances');
        $this->addRelation('CourseStudent', 'Zerebral\\BusinessBundle\\Model\\Course\\CourseStudent', RelationMap::ONE_TO_MANY, array('id' => 'student_id', ), 'CASCADE', 'CASCADE', 'CourseStudents');
        $this->addRelation('StudentGuardian', 'Zerebral\\BusinessBundle\\Model\\User\\StudentGuardian', RelationMap::ONE_TO_MANY, array('id' => 'student_id', ), 'CASCADE', 'CASCADE', 'StudentGuardians');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Assignments');
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Courses');
        $this->addRelation('Guardian', 'Zerebral\\BusinessBundle\\Model\\User\\Guardian', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Guardians');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'delegate' =>  array (
  'to' => 'users',
),
            'event' =>  array (
),
        );
    } // getBehaviors()

} // StudentTableMap
