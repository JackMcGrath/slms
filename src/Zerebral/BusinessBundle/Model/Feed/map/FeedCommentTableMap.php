<?php

namespace Zerebral\BusinessBundle\Model\Feed\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'feed_comments' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Feed.map
 */
class FeedCommentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Feed.map.FeedCommentTableMap';

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
        $this->setName('feed_comments');
        $this->setPhpName('FeedComment');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Feed\\FeedComment');
        $this->setPackage('Zerebral.BusinessBundle.Model.Feed');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('feed_item_id', 'FeedItemId', 'INTEGER', 'feed_items', 'id', true, null, null);
        $this->addForeignKey('feed_content_id', 'FeedContentId', 'INTEGER', 'feed_contents', 'id', true, null, null);
        $this->addForeignKey('created_by', 'CreatedBy', 'INTEGER', 'users', 'id', true, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('FeedItem', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem', RelationMap::MANY_TO_ONE, array('feed_item_id' => 'id', ), null, null);
        $this->addRelation('FeedContent', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedContent', RelationMap::MANY_TO_ONE, array('feed_content_id' => 'id', ), null, null);
        $this->addRelation('User', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('created_by' => 'id', ), null, null);
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

} // FeedCommentTableMap
