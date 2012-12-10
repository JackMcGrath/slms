<?php

namespace Zerebral\CommonBundle\Component;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Zerebral\BusinessBundle\Model\User\Teacher;
use Zerebral\BusinessBundle\Model\User\Student;

class Controller extends BaseController
{
    /**
     * @return null|Student|Teacher
     */
    public function getRoleUser()
    {
        $user = $this->getUser();

        if (is_null($user))
            return $user;

        return $user->getRoleModel();
    }
}
