<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseStudent;

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
}
