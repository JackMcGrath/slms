<?php

namespace Zerebral\BusinessBundle\Model\File\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'files' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.File.map
 */
class FileTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.File.map.FileTableMap';

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
        $this->setName('files');
        $this->setPhpName('File');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\File\\File');
        $this->setPackage('Zerebral.BusinessBundle.Model.File');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('path', 'Path', 'VARCHAR', true, 50, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 255, null);
        $this->addColumn('description', 'Description', 'VARCHAR', false, 255, null);
        $this->addColumn('size', 'Size', 'INTEGER', true, null, null);
        $this->addColumn('mime_type', 'MimeType', 'VARCHAR', true, 100, null);
        $this->addColumn('storage', 'Storage', 'CHAR', true, null, 'local');
        $this->getColumn('storage', false)->setValueSet(array (
  0 => 'local',
  1 => 'dropbox',
  2 => 's3',
));
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('AssignmentFile', 'Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentFile', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', 'CASCADE', 'AssignmentFiles');
        $this->addRelation('StudentAssignmentFile', 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignmentFile', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', 'CASCADE', 'StudentAssignmentFiles');
        $this->addRelation('CourseMaterial', 'Zerebral\\BusinessBundle\\Model\\Material\\CourseMaterial', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', 'CASCADE', 'CourseMaterials');
        $this->addRelation('MessageFile', 'Zerebral\\BusinessBundle\\Model\\Message\\MessageFile', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', 'CASCADE', 'MessageFiles');
        $this->addRelation('User', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::ONE_TO_MANY, array('id' => 'avatar_id', ), 'SET NULL', 'CASCADE', 'Users');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Assignments');
        $this->addRelation('StudentAssignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\StudentAssignment', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'StudentAssignments');
        $this->addRelation('Message', 'Zerebral\\BusinessBundle\\Model\\Message\\Message', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Messages');
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

} // FileTableMap
