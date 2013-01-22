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
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Student', 'Zerebral\\BusinessBundle\\Model\\User\\Student', RelationMap::MANY_TO_ONE, array('student_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::MANY_TO_ONE, array('assignment_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('StudentAssignmentFile', 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignmentFile', RelationMap::ONE_TO_MANY, array('id' => 'student_assignment_id', ), 'CASCADE', 'CASCADE', 'StudentAssignmentFiles');
        $this->addRelation('File', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Files');
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
