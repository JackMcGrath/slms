<?php

namespace Zerebral\BusinessBundle\Model\Attendance;

use Zerebral\BusinessBundle\Model\Attendance\om\BaseAttendance;
use Zerebral\BusinessBundle\Model\User\Student;

class Attendance extends BaseAttendance
{
    /**
     * @param $students
     */
    public function initStudents($students)
    {
        if ($this->isNew() && count($this->getStudentAttendances()) == 0) {
            foreach($students as $student) {
                $this->addStudentAttendance(new StudentAttendance($student));
            }
        }
    }
}
