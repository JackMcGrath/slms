<?php

namespace Zerebral\BusinessBundle\Model\Feed\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'feed_contents' table.
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
class FeedContentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Feed.map.FeedContentTableMap';

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
        $this->setName('feed_contents');
        $this->setPhpName('FeedContent');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Feed\\FeedContent');
        $this->setPackage('Zerebral.BusinessBundle.Model.Feed');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('type', 'Type', 'CHAR', true, null, null);
        $this->getColumn('type', false)->setValueSet(array (
  0 => 'video',
  1 => 'image',
  2 => 'website',
  3 => 'text',
  4 => 'assignment',
));
        $this->addColumn('text', 'Text', 'LONGVARCHAR', false, null, null);
        $this->addColumn('link_url', 'LinkUrl', 'VARCHAR', false, 150, null);
        $this->addColumn('link_title', 'LinkTitle', 'VARCHAR', false, 100, null);
        $this->addColumn('link_description', 'LinkDescription', 'VARCHAR', false, 255, null);
        $this->addColumn('link_thumbnail_url', 'LinkThumbnailUrl', 'VARCHAR', false, 150, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('FeedItem', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem', RelationMap::ONE_TO_MANY, array('id' => 'feed_content_id', ), 'CASCADE', 'CASCADE', 'FeedItems');
        $this->addRelation('FeedComment', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedComment', RelationMap::ONE_TO_MANY, array('id' => 'feed_content_id', ), 'CASCADE', 'CASCADE', 'FeedComments');
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

} // FeedContentTableMap
