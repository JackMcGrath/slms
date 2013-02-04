<?php

namespace Zerebral\BusinessBundle\Model\Attendance;

use Zerebral\BusinessBundle\Model\Attendance\om\BaseStudentAttendanceQuery;

class StudentAttendanceQuery extends BaseStudentAttendanceQuery
{
    public function filterByDateAndStudent($date, $student)
    {
        $this->leftJoinAttendance();
        $this->leftJoin('Attendance.Course Courses');
        $this->add('student_attendance.student_id', $student->getId());
        $this->add('attendance.date', $date);
        return $this;
    }
}
