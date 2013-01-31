<?php

namespace Zerebral\BusinessBundle\Model\Course\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'courses' table.
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
class CourseTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Zerebral.BusinessBundle.Model.Course.map.CourseTableMap';

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
        $this->setName('courses');
        $this->setPhpName('Course');
        $this->setClassname('Zerebral\\BusinessBundle\\Model\\Course\\Course');
        $this->setPackage('Zerebral.BusinessBundle.Model.Course');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('discipline_id', 'DisciplineId', 'TINYINT', 'disciplines', 'id', true, 2, null);
        $this->addForeignKey('grade_level_id', 'GradeLevelId', 'TINYINT', 'grade_levels', 'id', true, 2, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 100, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
        $this->addColumn('access_code', 'AccessCode', 'VARCHAR', false, 32, null);
        $this->addColumn('start', 'Start', 'DATE', false, null, null);
        $this->addColumn('end', 'End', 'DATE', false, null, null);
        $this->addForeignKey('created_by', 'CreatedBy', 'INTEGER', 'teachers', 'id', true, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', true, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CreatedByTeacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_ONE, array('created_by' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('Discipline', 'Zerebral\\BusinessBundle\\Model\\Course\\Discipline', RelationMap::MANY_TO_ONE, array('discipline_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('GradeLevel', 'Zerebral\\BusinessBundle\\Model\\Course\\GradeLevel', RelationMap::MANY_TO_ONE, array('grade_level_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('AssignmentCategory', 'Zerebral\\BusinessBundle\\Model\\Assignment\\AssignmentCategory', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'AssignmentCategories');
        $this->addRelation('Assignment', 'Zerebral\\BusinessBundle\\Model\\Assignment\\Assignment', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'Assignments');
        $this->addRelation('Attendance', 'Zerebral\\BusinessBundle\\Model\\Attendance\\Attendance', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'Attendances');
        $this->addRelation('CourseStudent', 'Zerebral\\BusinessBundle\\Model\\Course\\CourseStudent', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'CourseStudents');
        $this->addRelation('CourseTeacher', 'Zerebral\\BusinessBundle\\Model\\Course\\CourseTeacher', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'CourseTeachers');
        $this->addRelation('CourseScheduleDay', 'Zerebral\\BusinessBundle\\Model\\Course\\CourseScheduleDay', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'CourseScheduleDays');
        $this->addRelation('FeedItem', 'Zerebral\\BusinessBundle\\Model\\Feed\\FeedItem', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'FeedItems');
        $this->addRelation('CourseFolder', 'Zerebral\\BusinessBundle\\Model\\Material\\CourseFolder', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'CourseFolders');
        $this->addRelation('CourseMaterial', 'Zerebral\\BusinessBundle\\Model\\Material\\CourseMaterial', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'CourseMaterials');
        $this->addRelation('Notification', 'Zerebral\\BusinessBundle\\Model\\Notification\\Notification', RelationMap::ONE_TO_MANY, array('id' => 'course_id', ), 'CASCADE', 'CASCADE', 'Notifications');
        $this->addRelation('Student', 'Zerebral\\BusinessBundle\\Model\\User\\Student', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Students');
        $this->addRelation('Teacher', 'Zerebral\\BusinessBundle\\Model\\User\\Teacher', RelationMap::MANY_TO_MANY, array(), 'CASCADE', 'CASCADE', 'Teachers');
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

} // CourseTableMap
