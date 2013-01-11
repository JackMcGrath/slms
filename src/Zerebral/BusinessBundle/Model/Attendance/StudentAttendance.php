<?php

namespace Zerebral\BusinessBundle\Model\Attendance;

use Zerebral\BusinessBundle\Model\Attendance\om\BaseStudentAttendance;

class StudentAttendance extends BaseStudentAttendance
{
    public function __construct(\Zerebral\BusinessBundle\Model\User\Student $student = null)
    {
        $this->setStudent($student);
    }

    public function getUserName()
    {
        return $this->getStudent()->getFormattedName();
    }
}
