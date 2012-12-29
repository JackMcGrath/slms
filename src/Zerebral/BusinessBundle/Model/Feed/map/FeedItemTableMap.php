<?php

namespace Zerebral\BusinessBundle\Model\Feed\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'feed_items' table.
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
class FeedItemTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Feed.map.FeedItemTableMap';

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
        $this->setName('feed_items');
        $this->setPhpName('FeedItem');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem');
        $this->setPackage('Zerebral.BusinessBundle.Model.Feed');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('assignment_id', 'AssignmentId', 'INTEGER', 'assignments', 'id', false, null, null);
        $this->addForeignKey('course_id', 'CourseId', 'INTEGER', 'courses', 'id', false, null, null);
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
        $this->addRelation('FeedContent', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedContent', RelationMap::MANY_TO_ONE, array('feed_content_id' => 'id', ), null, null);
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::MANY_TO_ONE, array('assignment_id' => 'id', ), null, null);
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), null, null);
        $this->addRelation('User', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('created_by' => 'id', ), null, null);
        $this->addRelation('FeedComment', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedComment', RelationMap::ONE_TO_MANY, array('id' => 'feed_item_id', ), null, null, 'FeedComments');
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

} // FeedItemTableMap
