<?php

namespace Zerebral\BusinessBundle\Model\Material\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'course_materials' table.
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
class CourseMaterialTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Material.map.CourseMaterialTableMap';

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
        $this->setName('course_materials');
        $this->setPhpName('CourseMaterial');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Material\\CourseMaterial');
        $this->setPackage('Zerebral.BusinessBundle.Model.Material');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('course_id', 'CourseId', 'INTEGER', 'courses', 'id', true, null, null);
        $this->addForeignKey('folder_id', 'FolderId', 'INTEGER', 'course_folders', 'id', false, null, null);
        $this->addColumn('description', 'Description', 'VARCHAR', false, 255, null);
        $this->addForeignKey('file_id', 'FileId', 'INTEGER', 'files', 'id', true, null, null);
        $this->addForeignKey('created_by', 'CreatedBy', 'INTEGER', 'teachers', 'id', true, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
        $this->addValidator('id', 'required', 'propel.validator.RequiredValidator', '', 'The field id is required.');
        $this->addValidator('id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field id must be not greater than REPLACEME.');
        $this->addValidator('id', 'type', 'propel.validator.TypeValidator', 'int', 'The column id must be an int value.');
        $this->addValidator('course_id', 'required', 'propel.validator.RequiredValidator', '', 'The field course_id is required.');
        $this->addValidator('course_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field course_id must be not greater than REPLACEME.');
        $this->addValidator('course_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column course_id must be an int value.');
        $this->addValidator('folder_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field folder_id must be not greater than REPLACEME.');
        $this->addValidator('folder_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column folder_id must be an int value.');
        $this->addValidator('description', 'type', 'propel.validator.TypeValidator', 'string', 'The column description must be an string value.');
        $this->addValidator('file_id', 'required', 'propel.validator.RequiredValidator', '', 'The field file_id is required.');
        $this->addValidator('file_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field file_id must be not greater than REPLACEME.');
        $this->addValidator('file_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column file_id must be an int value.');
        $this->addValidator('created_by', 'required', 'propel.validator.RequiredValidator', '', 'The field created_by is required.');
        $this->addValidator('created_by', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field created_by must be not greater than REPLACEME.');
        $this->addValidator('created_by', 'type', 'propel.validator.TypeValidator', 'int', 'The column created_by must be an int value.');
        $this->addValidator('created_at', 'required', 'propel.validator.RequiredValidator', '', 'The field created_at is required.');
        $this->addValidator('created_at', 'type', 'propel.validator.TypeValidator', 'string', 'The column created_at must be an string value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Teacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_ONE, array('created_by' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('CourseFolder', 'Zerebral\\BusinessBundle\\Model\\Material\\CourseFolder', RelationMap::MANY_TO_ONE, array('folder_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('File', 'Zerebral\\BusinessBundle\\Model\\File\\File', RelationMap::MANY_TO_ONE, array('file_id' => 'id', ), 'CASCADE', 'CASCADE');
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

} // CourseMaterialTableMap
