<?php

namespace Zerebral\BusinessBundle\Model\Assignment\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'student_assignments' table.
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
class StudentAssignmentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Assignment.map.StudentAssignmentTableMap';

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
        $this->setName('student_assignments');
        $this->setPhpName('StudentAssignment');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignment');
        $this->setPackage('Zerebral.BusinessBundle.Model.Assignment');
        $this->setUseIdGenerator(true);
        $this->setIsCrossRef(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('student_id', 'StudentId', 'INTEGER', 'students', 'id', true, null, null);
        $this->addForeignKey('assignment_id', 'AssignmentId', 'INTEGER', 'assignments', 'id', true, null, null);
        $this->addColumn('is_submitted', 'IsSubmitted', 'BOOLEAN', true, 1, false);
        $this->addColumn('grading', 'Grading', 'VARCHAR', false, 10, null);
        $this->addColumn('grading_comment', 'GradingComment', 'LONGVARCHAR', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
        $this->addValidator('id', 'required', 'propel.validator.RequiredValidator', '', 'The field id is required.');
        $this->addValidator('id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field id must be not greater than REPLACEME.');
        $this->addValidator('id', 'type', 'propel.validator.TypeValidator', 'int', 'The column id must be an int value.');
        $this->addValidator('student_id', 'required', 'propel.validator.RequiredValidator', '', 'The field student_id is required.');
        $this->addValidator('student_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field student_id must be not greater than REPLACEME.');
        $this->addValidator('student_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column student_id must be an int value.');
        $this->addValidator('assignment_id', 'required', 'propel.validator.RequiredValidator', '', 'The field assignment_id is required.');
        $this->addValidator('assignment_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field assignment_id must be not greater than REPLACEME.');
        $this->addValidator('assignment_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column assignment_id must be an int value.');
        $this->addValidator('created_at', 'required', 'propel.validator.RequiredValidator', '', 'The field created_at is required.');
        $this->addValidator('created_at', 'type', 'propel.validator.TypeValidator', 'string', 'The column created_at must be an string value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Student', 'Zerebral\\BusinessBundle\\Model\\User\\Student', RelationMap::MANY_TO_ONE, array('student_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::MANY_TO_ONE, array('assignment_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('FileReferences', 'Zerebral\\BusinessBundle\\Model\\File\\FileReferences', RelationMap::ONE_TO_MANY, array('id' => 'reference_id', ), 'CASCADE', 'CASCADE', 'FileReferencess');
        $this->addRelation('File', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Files');
        $this->addRelation('assignmentReferenceId', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'assignmentReferenceIds');
        $this->addRelation('messageReferenceId', 'Zerebral\\BusinessBundle\\Model\\Message\\Message', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'messageReferenceIds');
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

} // StudentAssignmentTableMap
