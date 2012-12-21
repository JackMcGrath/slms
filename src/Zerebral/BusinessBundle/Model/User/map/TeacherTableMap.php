<?php

namespace Zerebral\BusinessBundle\Model\User\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'teachers' table.
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
class TeacherTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.User.map.TeacherTableMap';

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
        $this->setName('teachers');
        $this->setPhpName('Teacher');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\User\\Teacher');
        $this->setPackage('Zerebral.BusinessBundle.Model.User');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addColumn('bio', 'Bio', 'VARCHAR', false, 160, null);
        $this->addColumn('subjects', 'Subjects', 'LONGVARCHAR', false, null, null);
        $this->addColumn('grades', 'Grades', 'LONGVARCHAR', false, null, null);
        // validators
        $this->addValidator('bio', 'type', 'propel.validator.TypeValidator', 'string', 'The column bio must be an string value.');
        $this->addValidator('subjects', 'type', 'propel.validator.TypeValidator', 'string', 'The column subjects must be an string value.');
        $this->addValidator('grades', 'type', 'propel.validator.TypeValidator', 'string', 'The column grades must be an string value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), null, null);
        $this->addRelation('AssignmentCategory', 'Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentCategory', RelationMap::ONE_TO_MANY, array('id' => 'teacher_id', ), 'CASCADE', 'CASCADE', 'AssignmentCategories');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::ONE_TO_MANY, array('id' => 'teacher_id', ), 'CASCADE', 'CASCADE', 'Assignments');
        $this->addRelation('CreatedByTeacher', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::ONE_TO_MANY, array('id' => 'created_by', ), 'CASCADE', 'CASCADE', 'CreatedByTeachers');
        $this->addRelation('Discipline', 'Zerebral\\BusinessBundle\\Model\\Course\\Discipline', RelationMap::ONE_TO_MANY, array('id' => 'teacher_id', ), 'CASCADE', 'CASCADE', 'Disciplines');
        $this->addRelation('CourseTeacher', 'Zerebral\\BusinessBundle\\Model\\Course\\CourseTeacher', RelationMap::ONE_TO_MANY, array('id' => 'teacher_id', ), 'CASCADE', 'CASCADE', 'CourseTeachers');
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Courses');
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

} // TeacherTableMap
