<?php

namespace Zerebral\BusinessBundle\Model\Attendance;

use Zerebral\BusinessBundle\Model\Attendance\om\BaseAttendanceQuery;

class AttendanceQuery extends BaseAttendanceQuery
{
    public function filterByDateAndStudents($date, $student)
    {
        $this->filterByDate($date);



        return $this;
    }
}
