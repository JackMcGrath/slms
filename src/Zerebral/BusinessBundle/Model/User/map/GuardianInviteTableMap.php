<?php

namespace Zerebral\BusinessBundle\Model\User\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'guardian_invites' table.
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
class GuardianInviteTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.User.map.GuardianInviteTableMap';

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
        $this->setName('guardian_invites');
        $this->setPhpName('GuardianInvite');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\User\\GuardianInvite');
        $this->setPackage('Zerebral.BusinessBundle.Model.User');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('student_id', 'StudentId', 'INTEGER' , 'students', 'id', true, null, null);
        $this->addColumn('guardian_email', 'GuardianEmail', 'VARCHAR', true, 100, null);
        $this->addColumn('code', 'Code', 'VARCHAR', true, 32, null);
        $this->addColumn('activated', 'Activated', 'BOOLEAN', true, 1, false);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Student', 'Zerebral\\BusinessBundle\\Model\\User\\Student', RelationMap::MANY_TO_ONE, array('student_id' => 'id', ), 'CASCADE', 'CASCADE');
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

} // GuardianInviteTableMap
