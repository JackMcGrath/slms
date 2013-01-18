<?php

namespace Zerebral\BusinessBundle\Model\Attendance;

use Zerebral\BusinessBundle\Model\Attendance\om\BaseStudentAttendance;

class StudentAttendance extends BaseStudentAttendance
{
    const STATUS_PRESENT = 'present';

    public function __construct(\Zerebral\BusinessBundle\Model\User\Student $student = null)
    {
        $this->setStudent($student);
    }

    public function getUserName()
    {
        return $this->getStudent()->getFormattedName();
    }
}
