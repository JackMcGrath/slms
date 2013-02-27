<?php

namespace Zerebral\BusinessBundle\Model\Attendance;

use Zerebral\BusinessBundle\Model\Attendance\om\BaseAttendanceQuery;

class AttendanceQuery extends BaseAttendanceQuery
{
    public function filterByCourseAndDate($course, $date)
    {
        $this->leftJoinStudentAttendance('StudentAttendances');
        $this->filterByDate($date);
        $this->filterByCourse($course);

        $this->leftJoinCourse('Course');

        $this->leftJoin('Course.CourseStudent CourseStudent');
        $this->addJoinCondition('CourseStudent', '`CourseStudent`.is_active=1');
        $this->addJoinCondition('CourseStudent', '`CourseStudent`.student_id=`StudentAttendances`.student_id');
        $this->leftJoin('CourseStudent.Student Student');
        $this->leftJoin('Student.User User');
        $this->addJoinCondition('User', '`User`.is_active=1');
        $this->withColumn('`CourseStudent`.is_active', 'isActiveOnCourse');
        return $this;
    }
}
