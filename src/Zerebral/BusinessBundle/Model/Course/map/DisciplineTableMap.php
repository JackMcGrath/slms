<?php

namespace Zerebral\BusinessBundle\Model\Course\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'disciplines' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Course.map
 */
class DisciplineTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Course.map.DisciplineTableMap';

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
        $this->setName('disciplines');
        $this->setPhpName('Discipline');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Course\\Discipline');
        $this->setPackage('Zerebral.BusinessBundle.Model.Course');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'TINYINT', true, 2, null);
        $this->addForeignKey('teacher_id', 'TeacherId', 'INTEGER', 'teachers', 'id', false, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 50, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Teacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_ONE, array('teacher_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::ONE_TO_MANY, array('id' => 'discipline_id', ), 'CASCADE', 'CASCADE', 'Courses');
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

} // DisciplineTableMap
