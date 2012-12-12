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
}
