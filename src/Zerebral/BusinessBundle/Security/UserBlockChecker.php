<?php

namespace Zerebral\BusinessBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Zerebral\BusinessBundle\Model\User\User;

class UserBlockChecker extends \Symfony\Component\Security\Core\User\UserChecker
{
    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface|\Zerebral\BusinessBundle\Model\User\User $user
     * @throws \Symfony\Component\Security\Core\Exception\DisabledException
     * @return void
     */
    public function checkPostAuth(UserInterface $user)
    {
        if (!$user->getIsActive()) {
            throw new DisabledException('Your account has been blocked. Please contact support');
        }

        parent::checkPostAuth($user);
    }


}
