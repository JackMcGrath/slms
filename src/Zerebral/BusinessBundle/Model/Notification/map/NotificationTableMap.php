<?php

namespace Zerebral\BusinessBundle\Model\Notification\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'notifications' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Notification.map
 */
class NotificationTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Notification.map.NotificationTableMap';

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
        $this->setName('notifications');
        $this->setPhpName('Notification');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Notification\\Notification');
        $this->setPackage('Zerebral.BusinessBundle.Model.Notification');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addForeignKey('course_id', 'CourseId', 'INTEGER', 'courses', 'id', false, null, null);
        $this->addForeignKey('assignment_id', 'AssignmentId', 'INTEGER', 'assignments', 'id', false, null, null);
        $this->addColumn('type', 'Type', 'CHAR', false, null, null);
        $this->getColumn('type', false)->setValueSet(array (
  0 => 'assignment_create',
  1 => 'course_update',
  2 => 'assignment_update',
  3 => 'material_create',
  4 => 'assignment_file_create',
  5 => 'attendance_status',
  6 => 'course_feed_comment_create',
));
        $this->addColumn('params', 'Params', 'VARCHAR', false, 100, null);
        $this->addColumn('is_read', 'IsRead', 'BOOLEAN', true, 1, false);
        $this->addForeignKey('created_by', 'CreatedBy', 'INTEGER', 'users', 'id', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::MANY_TO_ONE, array('assignment_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('UserRelatedByCreatedBy', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('created_by' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('UserRelatedByUserId', 'Zerebral\\BusinessBundle\\Model\\User\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', 'CASCADE');
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

} // NotificationTableMap
