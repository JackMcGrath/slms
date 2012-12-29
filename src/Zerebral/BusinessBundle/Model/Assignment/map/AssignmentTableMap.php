<?php

namespace Zerebral\BusinessBundle\Model\Assignment\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'assignments' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Assignment.map
 */
class AssignmentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Assignment.map.AssignmentTableMap';

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
        $this->setName('assignments');
        $this->setPhpName('Assignment');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment');
        $this->setPackage('Zerebral.BusinessBundle.Model.Assignment');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('teacher_id', 'TeacherId', 'INTEGER', 'teachers', 'id', true, null, null);
        $this->addForeignKey('course_id', 'CourseId', 'INTEGER', 'courses', 'id', true, null, null);
        $this->addForeignKey('assignment_category_id', 'AssignmentCategoryId', 'TINYINT', 'assignment_categories', 'id', true, 2, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 200, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        $this->addColumn('max_points', 'MaxPoints', 'SMALLINT', true, 2, null);
        $this->addColumn('due_at', 'DueAt', 'TIMESTAMP', false, null, null);
        // validators
        $this->addValidator('id', 'required', 'propel.validator.RequiredValidator', '', 'The field id is required.');
        $this->addValidator('id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field id must be not greater than REPLACEME.');
        $this->addValidator('id', 'type', 'propel.validator.TypeValidator', 'int', 'The column id must be an int value.');
        $this->addValidator('teacher_id', 'required', 'propel.validator.RequiredValidator', '', 'The field teacher_id is required.');
        $this->addValidator('teacher_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field teacher_id must be not greater than REPLACEME.');
        $this->addValidator('teacher_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column teacher_id must be an int value.');
        $this->addValidator('course_id', 'required', 'propel.validator.RequiredValidator', '', 'The field course_id is required.');
        $this->addValidator('course_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field course_id must be not greater than REPLACEME.');
        $this->addValidator('course_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column course_id must be an int value.');
        $this->addValidator('assignment_category_id', 'required', 'propel.validator.RequiredValidator', '', 'The field assignment_category_id is required.');
        $this->addValidator('assignment_category_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field assignment_category_id must be not greater than REPLACEME.');
        $this->addValidator('assignment_category_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column assignment_category_id must be an int value.');
        $this->addValidator('name', 'required', 'propel.validator.RequiredValidator', '', 'The field name is required.');
        $this->addValidator('name', 'type', 'propel.validator.TypeValidator', 'string', 'The column name must be an string value.');
        $this->addValidator('description', 'type', 'propel.validator.TypeValidator', 'string', 'The column description must be an string value.');
        $this->addValidator('max_points', 'required', 'propel.validator.RequiredValidator', '', 'The field max_points is required.');
        $this->addValidator('max_points', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field max_points must be not greater than REPLACEME.');
        $this->addValidator('max_points', 'type', 'propel.validator.TypeValidator', 'int', 'The column max_points must be an int value.');
        $this->addValidator('due_at', 'type', 'propel.validator.TypeValidator', 'string', 'The column due_at must be an string value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Teacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_ONE, array('teacher_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('AssignmentCategory', 'Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentCategory', RelationMap::MANY_TO_ONE, array('assignment_category_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('StudentAssignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignment', RelationMap::ONE_TO_MANY, array('id' => 'assignment_id', ), 'CASCADE', 'CASCADE', 'StudentAssignments');
        $this->addRelation('FeedItem', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem', RelationMap::ONE_TO_MANY, array('id' => 'assignment_id', ), null, null, 'FeedItems');
        $this->addRelation('FileReferences', 'Zerebral\\BusinessBundle\\Model\\File\\FileReferences', RelationMap::ONE_TO_MANY, array('id' => 'reference_id', ), 'CASCADE', 'CASCADE', 'FileReferencess');
        $this->addRelation('Student', 'Zerebral\\BusinessBundle\\Model\\User\\Student', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Students');
        $this->addRelation('File', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Files');
        $this->addRelation('studentAssignmentReferenceId', 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignment', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'studentAssignmentReferenceIds');
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

} // AssignmentTableMap
