<?php

namespace Zerebral\BusinessBundle\Model\Attendance\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'student_attendance' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Zerebral.BusinessBundle.Model.Attendance.map
 */
class StudentAttendanceTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Attendance.map.StudentAttendanceTableMap';

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
        $this->setName('student_attendance');
        $this->setPhpName('StudentAttendance');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Attendance\\StudentAttendance');
        $this->setPackage('Zerebral.BusinessBundle.Model.Attendance');
        $this->setUseIdGenerator(false);
        // columns
        $this->addForeignPrimaryKey('attendance_id', 'AttendanceId', 'INTEGER' , 'attendance', 'id', true, null, null);
        $this->addForeignPrimaryKey('student_id', 'StudentId', 'INTEGER' , 'students', 'id', true, null, null);
        $this->addColumn('status', 'Status', 'CHAR', true, null, null);
        $this->getColumn('status', false)->setValueSet(array (
  0 => 'present',
  1 => 'tardy',
  2 => 'excused',
));
        $this->addColumn('comment', 'Comment', 'VARCHAR', false, 200, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Attendance', 'Zerebral\\BusinessBundle\\Model\\Attendance\\Attendance', RelationMap::MANY_TO_ONE, array('attendance_id' => 'id', ), 'CASCADE', 'CASCADE');
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

} // StudentAttendanceTableMap
