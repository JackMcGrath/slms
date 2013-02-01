<?php

namespace Zerebral\BusinessBundle\Model\Material\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'course_folders' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Material.map
 */
class CourseFolderTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Material.map.CourseFolderTableMap';

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
        $this->setName('course_folders');
        $this->setPhpName('CourseFolder');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Material\\CourseFolder');
        $this->setPackage('Zerebral.BusinessBundle.Model.Material');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('course_id', 'CourseId', 'INTEGER', 'courses', 'id', true, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', false, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('CourseMaterial', 'Zerebral\\BusinessBundle\\Model\\Material\\CourseMaterial', RelationMap::ONE_TO_MANY, array('id' => 'folder_id', ), 'CASCADE', 'CASCADE', 'CourseMaterials');
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

} // CourseFolderTableMap
