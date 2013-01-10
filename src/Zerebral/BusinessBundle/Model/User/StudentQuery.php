<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseStudentQuery;

class StudentQuery extends BaseStudentQuery
{
    public function getCourseAttendanceByDate($course, $date)
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
    public function getByCourse($course)
    {
        $this->leftJoinUser();
        $this->filterByCourse($course);
        $this->addAscendingOrderByColumn('LOWER(users.last_name)');
        return $this;
    }
}
