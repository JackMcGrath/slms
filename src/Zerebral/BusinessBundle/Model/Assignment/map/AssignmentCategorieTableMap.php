<?php

namespace Zerebral\BusinessBundle\Model\Assignment\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'assignment_categories' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Assignment.map
 */
class AssignmentCategorieTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Assignment.map.AssignmentCategorieTableMap';

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
        $this->setName('assignment_categories');
        $this->setPhpName('AssignmentCategorie');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentCategorie');
        $this->setPackage('Zerebral.BusinessBundle.Model.Assignment');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'TINYINT', true, 2, null);
        $this->addForeignKey('course_id', 'CourseId', 'INTEGER', 'courses', 'id', false, null, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 50, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
        $this->addValidator('id', 'required', 'propel.validator.RequiredValidator', '', 'The field id is required.');
        $this->addValidator('id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field id must be not greater than REPLACEME.');
        $this->addValidator('id', 'type', 'propel.validator.TypeValidator', 'int', 'The column id must be an int value.');
        $this->addValidator('course_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field course_id must be not greater than REPLACEME.');
        $this->addValidator('course_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column course_id must be an int value.');
        $this->addValidator('name', 'required', 'propel.validator.RequiredValidator', '', 'The field name is required.');
        $this->addValidator('name', 'type', 'propel.validator.TypeValidator', 'string', 'The column name must be an string value.');
        $this->addValidator('created_at', 'required', 'propel.validator.RequiredValidator', '', 'The field created_at is required.');
        $this->addValidator('created_at', 'type', 'propel.validator.TypeValidator', 'string', 'The column created_at must be an string value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), null, 'CASCADE');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::ONE_TO_MANY, array('id' => 'assignment_category_id', ), null, 'CASCADE', 'Assignments');
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

} // AssignmentCategorieTableMap
