<?php

namespace Zerebral\BusinessBundle\Model\Message\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'messages' table.
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
class MessageTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Message.map.MessageTableMap';

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
        $this->setName('messages');
        $this->setPhpName('Message');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Message\\Message');
        $this->setPackage('Zerebral.BusinessBundle.Model.Message');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('thread_id', 'ThreadId', 'BIGINT', true, 22, null);
        $this->addForeignKey('from_id', 'FromId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addForeignKey('to_id', 'ToId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addColumn('is_read', 'IsRead', 'BOOLEAN', true, 1, false);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addColumn('subject', 'Subject', 'VARCHAR', true, 255, null);
        $this->addColumn('body', 'Body', 'LONGVARCHAR', true, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('UserRelatedByUserId', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('UserRelatedByFromId', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('from_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('UserRelatedByToId', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('to_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('MessageFile', 'Zerebral\\BusinessBundle\\Model\\Message\\MessageFile', RelationMap::ONE_TO_MANY, array('id' => 'message_id', ), 'CASCADE', 'CASCADE', 'MessageFiles');
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

} // MessageTableMap
