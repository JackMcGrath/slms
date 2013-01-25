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
        $this->addColumn('max_points', 'MaxPoints', 'SMALLINT', false, 3, null);
        $this->addColumn('grade_type', 'GradeType', 'CHAR', true, null, 'numeric');
        $this->getColumn('grade_type', false)->setValueSet(array (
  0 => 'numeric',
  1 => 'pass',
));
        $this->addColumn('threshold', 'Threshold', 'SMALLINT', false, 3, null);
        $this->addColumn('due_at', 'DueAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Teacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_ONE, array('teacher_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('AssignmentCategory', 'Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentCategory', RelationMap::MANY_TO_ONE, array('assignment_category_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('AssignmentFile', 'Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentFile', RelationMap::ONE_TO_MANY, array('id' => 'assignment_id', ), 'CASCADE', 'CASCADE', 'AssignmentFiles');
        $this->addRelation('StudentAssignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignment', RelationMap::ONE_TO_MANY, array('id' => 'assignment_id', ), 'CASCADE', 'CASCADE', 'StudentAssignments');
        $this->addRelation('FeedItem', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem', RelationMap::ONE_TO_MANY, array('id' => 'assignment_id', ), 'CASCADE', 'CASCADE', 'FeedItems');
        $this->addRelation('Notification', 'Zerebral\\BusinessBundle\\Model\\Notification\\Notification', RelationMap::ONE_TO_MANY, array('id' => 'assignment_id', ), 'CASCADE', 'CASCADE', 'Notifications');
        $this->addRelation('File', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Files');
        $this->addRelation('Student', 'Zerebral\\BusinessBundle\\Model\\User\\Student', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Students');
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
