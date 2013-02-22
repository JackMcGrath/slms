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

//        $this->add('Courses.start', "'" . $dateRange['start'] . " 00:00:00' between Courses.start and Courses.end", \Criteria::CUSTOM);
//        $this->addOr('Courses.end', "'" . $dateRange['end'] . " 00:00:00' between Courses.start and Courses.end", \Criteria::CUSTOM);

        $this->add('Courses.start', "Courses.start between '" . $dateRange['start'] . " 00:00:00' and '" . $dateRange['end'] . " 00:00:00'", \Criteria::CUSTOM);
        $this->addOr('Courses.end', "Courses.end between '" . $dateRange['start'] . " 00:00:00' and '" . $dateRange['end'] . " 00:00:00'", \Criteria::CUSTOM);

        $this->addOr('Courses.end', null, \Criteria::ISNULL);
        $this->addOr('Courses.start', null, \Criteria::ISNULL);

        $this->addAscendingOrderByColumn('LOWER(Courses.name)');
        return $this;
    }
}
