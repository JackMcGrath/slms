<?php

namespace Zerebral\BusinessBundle\Model\Assignment\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'assignment_files' table.
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
class AssignmentFileTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Assignment.map.AssignmentFileTableMap';

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
        $this->setName('assignment_files');
        $this->setPhpName('AssignmentFile');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentFile');
        $this->setPackage('Zerebral.BusinessBundle.Model.Assignment');
        $this->setUseIdGenerator(false);
        $this->setIsCrossRef(true);
        // columns
        $this->addForeignPrimaryKey('file_id', 'fileId', 'INTEGER' , 'files', 'id', true, null, null);
        $this->addForeignPrimaryKey('assignment_id', 'assignmentId', 'INTEGER' , 'assignments', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('File', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::MANY_TO_ONE, array('assignment_id' => 'id', ), 'CASCADE', 'CASCADE');
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

} // AssignmentFileTableMap
