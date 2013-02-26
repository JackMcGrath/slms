<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseStudentQuery;

class StudentQuery extends BaseStudentQuery
{
    public function findCourseAttendanceByDate($course, $date)
    {
        $this->leftJoinUser();
        $this->innerJoin('CourseStudent course_students');
        $this->addJoinCondition('course_students', 'course_students.course_id = "'.$course->getId().'"', \Criteria::EQUAL);

        $this->leftJoinStudentAttendance();
        $this->innerJoin('StudentAttendance.Attendance attendance');
        $this->addJoinCondition('attendance', 'attendance.date="'. $date .'"');
        $this->addJoinCondition('attendance', 'attendance.course_id="'.$course->getId().'"');

        $this->withColumn('student_attendance.status', 'status');
        $this->withColumn('student_attendance.comment', 'comment');

        $this->addAscendingOrderByColumn('LOWER(users.last_name)');

        $this->groupBy('course_students.student_id');

        return $this;
    }

    /**
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @return mixed
     */
    public function findByCourse($course, $isActive = true)
    {
        $this->leftJoinUser();
        $this->filterByCourse($course);
        $this->leftJoinCourseStudent();

        $this->add('course_students.is_active', $isActive);
        $this->add('users.is_active', true);

        $this->withColumn('course_students.is_active', 'isActiveOnCourse');

        $this->addAscendingOrderByColumn('LOWER(users.last_name)');
        $this->addGroupByColumn('course_students.student_id');
        return $this;
    }

    /**
     * @param \Zerebral\BusinessBundle\Model\Assignment\Assignment $course
     * @return mixed
     */
    public function findByAssignment($assignment, $isActive = true)
    {
        $this->leftJoinUser();
        $this->filterByAssignment($assignment);
        $this->leftJoinStudentAssignment();
        $this->leftJoin('StudentAssignment.Assignment Assignments');
        $this->leftJoin('Assignments.Course Course');
        $this->leftJoin('Course.CourseStudent CourseStudents');

        $this->where('students.id=`CourseStudents`.student_id');
        $this->add('CourseStudents.is_active', $isActive);
        $this->add('users.is_active', true);
        $this->withColumn('`CourseStudents`.is_active', 'isActiveOnCourse');

        $this->addAscendingOrderByColumn('LOWER(users.last_name)');

        return $this;
    }
}
