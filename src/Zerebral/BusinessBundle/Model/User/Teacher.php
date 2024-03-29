<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseTeacher;

class Teacher extends BaseTeacher
{
    public function preSave(\PropelPDO $con = null)
    {
        if ($this->getUser($con) !== null && $this->getUser($con)->isNew()) {
            $this->getUser($con)->setRole(User::ROLE_TEACHER);
        }
        return parent::preSave($con);
    }

    public function getFullName()
    {
        return $this->getUser()->getFullName();
    }

    public function getFormattedName()
    {
        return $this->getUser()->getFormattedName();
    }

    public function getCourseAssignments(\Zerebral\BusinessBundle\Model\Course\Course $course)
    {
        return $course->getAssignments();
    }

    public function getCourseAssignmentsDraft(\Zerebral\BusinessBundle\Model\Course\Course $course)
    {
        $c = new \Criteria();
        $c->addJoin(\Zerebral\BusinessBundle\Model\Assignment\AssignmentPeer::ID, \Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer::ASSIGNMENT_ID, \Criteria::LEFT_JOIN);
        $c->add('student_assignments.id', null, \Criteria::ISNULL);
        $c->addDescendingOrderByColumn('due_at');

        return $course->getAssignments($c);
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
