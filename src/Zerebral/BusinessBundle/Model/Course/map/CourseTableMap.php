<?php

namespace Zerebral\BusinessBundle\Model\Course\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'courses' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Course.map
 */
class CourseTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Course.map.CourseTableMap';

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
        $this->setName('courses');
        $this->setPhpName('Course');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Course\\Course');
        $this->setPackage('Zerebral.BusinessBundle.Model.Course');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('discipline_id', 'DisciplineId', 'TINYINT', 'disciplines', 'id', true, 2, null);
        $this->addForeignKey('grade_level_id', 'GradeLevelId', 'TINYINT', 'grade_levels', 'id', true, 2, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 100, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        $this->addForeignKey('created_by', 'CreatedBy', 'INTEGER', 'teachers', 'id', true, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
        $this->addValidator('id', 'required', 'propel.validator.RequiredValidator', '', 'The field id is required.');
        $this->addValidator('id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field id must be not greater than REPLACEME.');
        $this->addValidator('id', 'type', 'propel.validator.TypeValidator', 'int', 'The column id must be an int value.');
        $this->addValidator('discipline_id', 'required', 'propel.validator.RequiredValidator', '', 'The field discipline_id is required.');
        $this->addValidator('discipline_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column discipline_id must be an int value.');
        $this->addValidator('grade_level_id', 'required', 'propel.validator.RequiredValidator', '', 'The field grade_level_id is required.');
        $this->addValidator('grade_level_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column grade_level_id must be an int value.');
        $this->addValidator('name', 'required', 'propel.validator.RequiredValidator', '', 'The field name is required.');
        $this->addValidator('name', 'type', 'propel.validator.TypeValidator', 'string', 'The column name must be an string value.');
        $this->addValidator('description', 'type', 'propel.validator.TypeValidator', 'string', 'The column description must be an string value.');
        $this->addValidator('created_by', 'required', 'propel.validator.RequiredValidator', '', 'The field created_by is required.');
        $this->addValidator('created_by', 'type', 'propel.validator.TypeValidator', 'int', 'The column created_by must be an int value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreatedByTeacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_ONE, array('created_by' => 'id', ), null, 'CASCADE');
        $this->addRelation('Discipline', 'Zerebral\\BusinessBundle\\Model\\Course\\Discipline', RelationMap::MANY_TO_ONE, array('discipline_id' => 'id', ), null, 'CASCADE');
        $this->addRelation('GradeLevel', 'Zerebral\\BusinessBundle\\Model\\Course\\GradeLevel', RelationMap::MANY_TO_ONE, array('grade_level_id' => 'id', ), null, 'CASCADE');
        $this->addRelation('AssignmentCategory', 'Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentCategory', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), null, 'CASCADE', 'AssignmentCategories');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), null, 'CASCADE', 'Assignments');
        $this->addRelation('CourseStudent', 'Zerebral\\BusinessBundle\\Model\\Course\\CourseStudent', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), null, 'CASCADE', 'CourseStudents');
        $this->addRelation('CourseTeacher', 'Zerebral\\BusinessBundle\\Model\\Course\\CourseTeacher', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), null, 'CASCADE', 'CourseTeachers');
        $this->addRelation('Student', 'Zerebral\\BusinessBundle\\Model\\User\\Student', RelationMap::MANY_TO_MANY, array(), null, 'CASCADE', 'Students');
        $this->addRelation('Teacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_MANY, array(), null, 'CASCADE', 'Teachers');
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
            'event' =>  array (
),
        );
    } // getBehaviors()

} // CourseTableMap
