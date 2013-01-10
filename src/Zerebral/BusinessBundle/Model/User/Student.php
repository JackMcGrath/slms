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

    public function getFormattedName()
    {
        return $this->getUser()->getFormattedName();
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

    /**
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @param null $ongoing if null - get all, false - only with due_at, true - only without due at
     * @return \PropelObjectCollection
     */
    public function getCourseAssignmentsDueDate(\Zerebral\BusinessBundle\Model\Course\Course $course, $ongoing = null)
    {
        $c = new \Criteria();
        $c->addDescendingOrderByColumn('due_at');
        if ($ongoing === false) {
            $c->add('due_at', null, \Criteria::ISNOTNULL);
        } else if ($ongoing === true) {
            $c->add('due_at', null, \Criteria::ISNULL);
        }

        $assignments = new \PropelObjectCollection();
        $assignments->setModel('Zerebral\BusinessBundle\Model\Assignment\Assignment');
        foreach ($this->getAssignments($c) as $assignment) {
            if ($assignment->getCourseId() == $course->getId()) {
                $assignments->append($assignment);
            }
        }
        return $assignments;
    }

    public function getAssignmentsDueDate($ongoing = null)
    {
        $c = new \Criteria();
        $c->addDescendingOrderByColumn('due_at');
        if ($ongoing === false) {
            $c->add('due_at', null, \Criteria::ISNOTNULL);
        } else if ($ongoing === true) {
            $c->add('due_at', null, \Criteria::ISNULL);
        }

        return $this->getAssignments($c);
    }

    public function hasCourse(\Zerebral\BusinessBundle\Model\Course\Course $course)
    {
        foreach($this->getCourses() as $course){
            if($course->getId() == $course->getId()){
                return true;
            }
        }
        return false;
    }
}
