<?php

namespace Zerebral\BusinessBundle\Model\User\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'users' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.User.map
 */
class UserTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.User.map.UserTableMap';

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
        $this->setName('users');
        $this->setPhpName('User');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\User\\User');
        $this->setPackage('Zerebral.BusinessBundle.Model.User');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('role', 'Role', 'VARCHAR', true, null, null);
        $this->getColumn('role', false)->setValueSet(array (
  0 => 'director',
  1 => 'parent',
  2 => 'teacher',
  3 => 'student',
));
        $this->addColumn('first_name', 'FirstName', 'VARCHAR', true, 100, null);
        $this->addColumn('last_name', 'LastName', 'VARCHAR', true, 100, null);
        $this->addColumn('salutation', 'Salutation', 'VARCHAR', false, 5, null);
        $this->addColumn('birthday', 'Birthday', 'DATE', false, null, null);
        $this->addColumn('gender', 'Gender', 'CHAR', false, null, null);
        $this->getColumn('gender', false)->setValueSet(array (
  0 => 'male',
  1 => 'female',
));
        $this->addColumn('email', 'Email', 'VARCHAR', true, 100, null);
        $this->addColumn('password', 'Password', 'VARCHAR', true, 40, null);
        $this->addColumn('salt', 'Salt', 'VARCHAR', true, 32, null);
        $this->addForeignKey('avatar_id', 'AvatarId', 'INTEGER', 'files', 'id', false, null, null);
        $this->addColumn('is_active', 'IsActive', 'BOOLEAN', true, 1, true);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Avatar', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_ONE, array('avatar_id' => 'id', ), 'SET NULL', 'CASCADE');
        $this->addRelation('FeedItem', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem', RelationMap::ONE_TO_MANY, array('id' => 'created_by', ), 'CASCADE', 'CASCADE', 'FeedItems');
        $this->addRelation('FeedComment', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedComment', RelationMap::ONE_TO_MANY, array('id' => 'created_by', ), 'CASCADE', 'CASCADE', 'FeedComments');
        $this->addRelation('MessageRelatedByUserId', 'Zerebral\\BusinessBundle\\Model\\Message\\Message', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', 'CASCADE', 'MessagesRelatedByUserId');
        $this->addRelation('MessageRelatedByFromId', 'Zerebral\\BusinessBundle\\Model\\Message\\Message', RelationMap::ONE_TO_MANY, array('id' => 'from_id', ), 'CASCADE', 'CASCADE', 'MessagesRelatedByFromId');
        $this->addRelation('MessageRelatedByToId', 'Zerebral\\BusinessBundle\\Model\\Message\\Message', RelationMap::ONE_TO_MANY, array('id' => 'to_id', ), 'CASCADE', 'CASCADE', 'MessagesRelatedByToId');
        $this->addRelation('NotificationRelatedByCreatedBy', 'Zerebral\\BusinessBundle\\Model\\Notification\\Notification', RelationMap::ONE_TO_MANY, array('id' => 'created_by', ), 'CASCADE', 'CASCADE', 'NotificationsRelatedByCreatedBy');
        $this->addRelation('NotificationRelatedByUserId', 'Zerebral\\BusinessBundle\\Model\\Notification\\Notification', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', 'CASCADE', 'NotificationsRelatedByUserId');
        $this->addRelation('Student', 'Zerebral\\BusinessBundle\\Model\\User\\Student', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), null, null, 'Students');
        $this->addRelation('Teacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), null, null, 'Teachers');
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

} // UserTableMap
