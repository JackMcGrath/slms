<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignment;

class Assignment extends BaseAssignment
{
    private $gradeTypeIsModified = false;

    public function __construct()
    {
        $this->setMaxPoints(100);
        parent::__construct();
    }


    public function preDelete(\PropelPDO $con = null)
    {
        foreach ($this->getFiles() as $file) {
            $file->delete();
        }
        return parent::preDelete($con);
    }

    public function preSave(\PropelPDO $con = null)
    {
        $this->gradeTypeIsModified = true;
        return parent::preSave($con);
    }

    public function postSave(\PropelPDO $con = null)
    {
        if ($this->gradeTypeIsModified) {
            StudentAssignmentQuery::create()->filterByAssignmentId($this->getId())->update(array('Grading' => null));
        }
        return parent::postSave($con);
    }

    /** @return \Zerebral\BusinessBundle\Model\Feed\FeedItem */
    public function getFeedItem()
    {
        return $this->getFeedItems()->getFirst();
    }

    public function hasNotificationByType($type)
    {
        $c = new \Criteria();
        $c->add('type', $type);
        return (bool)$this->getNotifications($c)->count();
    }

    /** @param \Zerebral\BusinessBundle\Model\User\Student $student */
    public function getStudentAssignmentByStudent($student)
    {
        return StudentAssignmentQuery::create()->filterByStudent($student)->filterByAssignment($this)->findOne();
    }
}
