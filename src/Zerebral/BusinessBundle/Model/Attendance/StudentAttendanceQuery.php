<?php

namespace Zerebral\BusinessBundle\Model\Attendance;

use Zerebral\BusinessBundle\Model\Attendance\om\BaseStudentAttendanceQuery;

class StudentAttendanceQuery extends BaseStudentAttendanceQuery
{
    public function filterByDateAndStudent($dateRange, $student)
    {
        $this->leftJoinAttendance();
        $this->leftJoin('Attendance.Course Courses');
        $this->add('student_attendance.student_id', $student->getId());
        $this->add('attendance.date', $dateRange['start'], \Criteria::GREATER_EQUAL);
        $this->add('attendance.date', $dateRange['end'], \Criteria::LESS_EQUAL);
        $this->addAscendingOrderByColumn('LOWER(Courses.name)');
        return $this;
    }
}
