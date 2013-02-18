<?php

namespace Zerebral\BusinessBundle\Security;

use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Zerebral\BusinessBundle\Model\User\User;

class ProfileAccessVoter extends \Zerebral\CommonBundle\Security\ModelAccessVoter
{
    protected function getSupportedAttributes()
    {
        return array('VIEW');
    }

    protected function getModelClass()
    {
        return '\Zerebral\BusinessBundle\Model\User\User';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Zerebral\BusinessBundle\Model\User\User $profile
     * @param $attribute
     */
    public function isGranted(TokenInterface $token, $profile, $attribute)
    {
        /** @var Uer $user */

        $user = $token->getUser();

        /** @var User $relatedUser */
        foreach ($user->getRelatedUsers() as $relatedUser) {
            if ($relatedUser->getId() == $profile->getId())
                return true;
        }

        return false;
    }
}
