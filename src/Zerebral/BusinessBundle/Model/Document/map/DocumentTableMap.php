<?php

namespace Zerebral\BusinessBundle\Model\Document\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'documents' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Document.map
 */
class DocumentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Document.map.DocumentTableMap';

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
        $this->setName('documents');
        $this->setPhpName('Document');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Document\\Document');
        $this->setPackage('Zerebral.BusinessBundle.Model.Document');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('reference_id', 'ReferenceId', 'INTEGER', true, null, null);
        $this->addPrimaryKey('type', 'Type', 'VARCHAR', true, null, null);
        $this->addColumn('storage', 'Storage', 'VARCHAR', true, null, null);
        $this->addColumn('path', 'Path', 'VARCHAR', true, 200, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        // validators
        $this->addValidator('reference_id', 'required', 'propel.validator.RequiredValidator', '', 'The field reference_id is required.');
        $this->addValidator('reference_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field reference_id must be not greater than REPLACEME.');
        $this->addValidator('reference_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column reference_id must be an int value.');
        $this->addValidator('type', 'required', 'propel.validator.RequiredValidator', '', 'The field type is required.');
        $this->addValidator('type', 'type', 'propel.validator.TypeValidator', 'string', 'The column type must be an string value.');
        $this->addValidator('storage', 'required', 'propel.validator.RequiredValidator', '', 'The field storage is required.');
        $this->addValidator('storage', 'type', 'propel.validator.TypeValidator', 'string', 'The column storage must be an string value.');
        $this->addValidator('path', 'required', 'propel.validator.RequiredValidator', '', 'The field path is required.');
        $this->addValidator('path', 'type', 'propel.validator.TypeValidator', 'string', 'The column path must be an string value.');
        $this->addValidator('created_at', 'required', 'propel.validator.RequiredValidator', '', 'The field created_at is required.');
        $this->addValidator('created_at', 'type', 'propel.validator.TypeValidator', 'string', 'The column created_at must be an string value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
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

} // DocumentTableMap
