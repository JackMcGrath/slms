<?php

namespace Zerebral\BusinessBundle\Model\Message\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'message_files' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Message.map
 */
class MessageFileTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Message.map.MessageFileTableMap';

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
        $this->setName('message_files');
        $this->setPhpName('MessageFile');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Message\\MessageFile');
        $this->setPackage('Zerebral.BusinessBundle.Model.Message');
        $this->setUseIdGenerator(false);
        $this->setIsCrossRef(true);
        // columns
        $this->addForeignPrimaryKey('file_id', 'fileId', 'INTEGER' , 'files', 'id', true, null, null);
        $this->addForeignPrimaryKey('message_id', 'messageId', 'INTEGER' , 'messages', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('File', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Message', 'Zerebral\\BusinessBundle\\Model\\Message\\Message', RelationMap::MANY_TO_ONE, array('message_id' => 'id', ), 'CASCADE', 'CASCADE');
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

} // MessageFileTableMap
