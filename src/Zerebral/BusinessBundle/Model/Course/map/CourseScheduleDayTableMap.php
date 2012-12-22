<?php

namespace Zerebral\BusinessBundle\Model\Course\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'course_schedule_days' table.
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
class CourseScheduleDayTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Course.map.CourseScheduleDayTableMap';

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
        $this->setName('course_schedule_days');
        $this->setPhpName('CourseScheduleDay');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Course\\CourseScheduleDay');
        $this->setPackage('Zerebral.BusinessBundle.Model.Course');
        $this->setUseIdGenerator(true);
        $this->setIsCrossRef(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignPrimaryKey('course_id', 'CourseId', 'INTEGER' , 'courses', 'id', true, null, null);
        $this->addColumn('week_day', 'WeekDay', 'CHAR', true, null, null);
        $this->addColumn('time_from', 'TimeFrom', 'TIME', true, null, null);
        $this->addColumn('time_to', 'TimeTo', 'TIME', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Course', 'Zerebral\\BusinessBundle\\Model\\Course\\Course', RelationMap::MANY_TO_ONE, array('course_id' => 'id', ), 'CASCADE', 'CASCADE');
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

} // CourseScheduleDayTableMap
