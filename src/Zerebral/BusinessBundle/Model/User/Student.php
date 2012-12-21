<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseStudent;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;

class Student extends BaseStudent
{
    public function preSave(\PropelPDO $con = null)
    {
        if ($this->getUser($con) !== null && $this->getUser($con)->isNew()) {
            $this->getUser($con)->setRole(User::ROLE_STUDENT);
        }
        return parent::preSave($con);
    }

    public function getFullName()
    {
        return $this->getUser()->getFullName();
    }

    public function getCourseAssignments(\Zerebral\BusinessBundle\Model\Course\Course $course)
    {
        $assignments = new \PropelObjectCollection();
        $assignments->setModel('Zerebral\BusinessBundle\Model\Assignment\Assignment');
        foreach ($this->getAssignments() as $assignment) {
            if ($assignment->getCourseId() == $course->getId()) {
                $assignments->append($assignment);
            }
        }
        return $assignments;
    }

    public function hasCourse(\Zerebral\BusinessBundle\Model\Course\Course $course)
    {
        foreach($this->getCourses() as $c){
            if($course->getId() == $c->getId()){
                return true;
            }
        }
        return false;
    }
}
