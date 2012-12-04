<?php

namespace Zerebral\BusinessBundle\Model\Course\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'course_teachers' table.
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
class CourseTeacherTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Course.map.CourseTeacherTableMap';

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
        $this->setName('course_teachers');
        $this->setPhpName('CourseTeacher');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Course\\CourseTeacher');
        $this->setPackage('Zerebral.BusinessBundle.Model.Course');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('course_id', 'CourseId', 'INTEGER' , 'courses', 'id', true, null, null);
        $this->addForeignPrimaryKey('teacher_id', 'TeacherId', 'INTEGER' , 'teachers', 'id', true, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        // validators
        $this->addValidator('course_id', 'required', 'propel.validator.RequiredValidator', '', 'The field course_id is required.');
        $this->addValidator('course_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field course_id must be not greater than REPLACEME.');
        $this->addValidator('course_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column course_id must be an int value.');
        $this->addValidator('teacher_id', 'required', 'propel.validator.RequiredValidator', '', 'The field teacher_id is required.');
        $this->addValidator('teacher_id', 'maxValue', 'propel.validator.MaxValueValidator', 'REPLACEME', 'The field teacher_id must be not greater than REPLACEME.');
        $this->addValidator('teacher_id', 'type', 'propel.validator.TypeValidator', 'int', 'The column teacher_id must be an int value.');
        $this->addValidator('created_at', 'type', 'propel.validator.TypeValidator', 'string', 'The column created_at must be an string value.');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), null, 'CASCADE');
        $this->addRelation('Teacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_ONE, array('teacher_id' => 'id', ), null, 'CASCADE');
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

} // CourseTeacherTableMap
