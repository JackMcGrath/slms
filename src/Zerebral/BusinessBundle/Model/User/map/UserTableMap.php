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
        $this->addColumn('first_name', 'FirstName', 'VARCHAR', true, 100, null);
        $this->addColumn('last_name', 'LastName', 'VARCHAR', true, 100, null);
        $this->addColumn('salutation', 'Salutation', 'VARCHAR', false, 5, null);
        $this->addColumn('birthday', 'Birthday', 'DATE', false, null, null);
        $this->addColumn('gender', 'Gender', 'CHAR', false, null, null);
        $this->addColumn('email', 'Email', 'VARCHAR', true, 100, null);
        $this->addColumn('password', 'Password', 'VARCHAR', true, 40, null);
        $this->addColumn('salt', 'Salt', 'VARCHAR', true, 32, null);
        $this->addForeignKey('avatar_id', 'AvatarId', 'INTEGER', 'files', 'id', false, null, null);
        $this->addColumn('is_active', 'IsActive', 'BOOLEAN', true, 1, true);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', true, null, null);
        // validators
        $this->addValidator('id', 'required', 'propel.validator.RequiredValidator', '', 'The field id is required.');
        $this->addValidator('id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field id must be not greater than REPLACEME.');
        $this->addValidator('id', 'type', 'propel.validator.TypeValidator', 'int', 'The column id must be an int value.');
        $this->addValidator('first_name', 'required', 'propel.validator.RequiredValidator', '', 'The field first_name is required.');
        $this->addValidator('first_name', 'type', 'propel.validator.TypeValidator', 'string', 'The column first_name must be an string value.');
        $this->addValidator('last_name', 'required', 'propel.validator.RequiredValidator', '', 'The field last_name is required.');
        $this->addValidator('last_name', 'type', 'propel.validator.TypeValidator', 'string', 'The column last_name must be an string value.');
        $this->addValidator('salutation', 'type', 'propel.validator.TypeValidator', 'string', 'The column salutation must be an string value.');
        $this->addValidator('birthday', 'type', 'propel.validator.TypeValidator', 'string', 'The column birthday must be an string value.');
        $this->addValidator('gender', 'type', 'propel.validator.TypeValidator', 'string', 'The column gender must be an string value.');
        $this->addValidator('email', 'required', 'propel.validator.RequiredValidator', '', 'The field email is required.');
        $this->addValidator('email', 'type', 'propel.validator.TypeValidator', 'string', 'The column email must be an string value.');
        $this->addValidator('salt', 'required', 'propel.validator.RequiredValidator', '', 'The field salt is required.');
        $this->addValidator('salt', 'type', 'propel.validator.TypeValidator', 'string', 'The column salt must be an string value.');
        $this->addValidator('is_active', 'required', 'propel.validator.RequiredValidator', '', 'The field is_active is required.');
        $this->addValidator('is_active', 'type', 'propel.validator.TypeValidator', 'boolean', 'The column is_active must be an boolean value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Avatar', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_ONE, array('avatar_id' => 'id', ), 'SET NULL', 'CASCADE');
        $this->addRelation('FeedItem', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem', RelationMap::ONE_TO_MANY, array('id' => 'created_by', ), null, null, 'FeedItems');
        $this->addRelation('FeedComment', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedComment', RelationMap::ONE_TO_MANY, array('id' => 'created_by', ), null, null, 'FeedComments');
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
