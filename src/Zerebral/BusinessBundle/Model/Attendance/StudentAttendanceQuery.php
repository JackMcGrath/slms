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
        //$this->add('attendance.date', $dateRange['start'], \Criteria::GREATER_EQUAL);
        //$this->addAnd('attendance.date', $dateRange['end'], \Criteria::LESS_EQUAL);



        $this->add('Courses.start', "'" . $dateRange['start'] . " 00:00:00' between Courses.start and Courses.end", \Criteria::CUSTOM);
        $this->addOr('Courses.end', "'" . $dateRange['end'] . " 00:00:00' between Courses.start and Courses.end", \Criteria::CUSTOM);
        $this->addOr('Courses.end', null, \Criteria::ISNULL);
        $this->addOr('Courses.start', null, \Criteria::ISNULL);
        //$this->addAnd('Courses.end', $dateRange['end'], \Criteria::LESS_EQUAL);

//        $this->add('Courses.start', $dateRange['start'], \Criteria::GREATER_EQUAL);
//        $this->addAnd('Courses.end', $dateRange['end'], \Criteria::LESS_EQUAL);



        $this->addAscendingOrderByColumn('LOWER(Courses.name)');
        return $this;
    }
}
