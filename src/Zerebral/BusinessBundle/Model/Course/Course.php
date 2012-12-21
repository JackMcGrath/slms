<?php

namespace Zerebral\BusinessBundle\Model\Course;

use Zerebral\BusinessBundle\Model\Course\om\BaseCourse;

class Course extends BaseCourse
{
    /**
     * {@inheritDoc}
     */
    public function preSave(\PropelPDO $con = null)
    {
        //@todo fix it
        $this->setUpdatedAt(date("Y-m-d H:i:s", time()));
        return parent::preSave();
    }

    public function preInsert(\PropelPDO $con = null)
    {
        //@todo fix it
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        $this->setAccessCode($this->generateInvite());
        return parent::preInsert($con);
    }

    public function getTeacher()
    {
        return $this->getTeachers()->getFirst();
    }

    /**
     * Generate random string for use it as course invite code
     * @param int $length
     * @return string
     */
    private function generateInvite($length = 7) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    public function resetAccessCode(){
        $this->setAccessCode($this->generateInvite());
    }
}
