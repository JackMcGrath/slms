<?php

namespace Zerebral\BusinessBundle\Model\Course\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'grade_levels' table.
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
class GradeLevelTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Course.map.GradeLevelTableMap';

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
        $this->setName('grade_levels');
        $this->setPhpName('GradeLevel');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Course\\GradeLevel');
        $this->setPackage('Zerebral.BusinessBundle.Model.Course');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'TINYINT', true, 2, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 50, null);
        // validators
        $this->addValidator('id', 'required', 'propel.validator.RequiredValidator', '', 'The field id is required.');
        $this->addValidator('id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field id must be not greater than REPLACEME.');
        $this->addValidator('id', 'type', 'propel.validator.TypeValidator', 'int', 'The column id must be an int value.');
        $this->addValidator('name', 'required', 'propel.validator.RequiredValidator', '', 'The field name is required.');
        $this->addValidator('name', 'type', 'propel.validator.TypeValidator', 'string', 'The column name must be an string value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::ONE_TO_MANY, array('id' => 'grade_level_id', ), 'CASCADE', 'CASCADE', 'Courses');
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

} // GradeLevelTableMap
