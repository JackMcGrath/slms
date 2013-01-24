<?php

namespace Zerebral\BusinessBundle\Security;

use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Zerebral\BusinessBundle\Model\User\User;

class CourseMaterialAccessVoter extends \Zerebral\CommonBundle\Security\ModelAccessVoter
{
    protected function getSupportedAttributes()
    {
        return array('DELETE');
    }

    protected function getModelClass()
    {
        return '\Zerebral\BusinessBundle\Model\Course\CourseMaterial';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Zerebral\BusinessBundle\Model\Material\CourseMaterial $courseMaterial
     * @param $attribute
     */
    public function isGranted(TokenInterface $token, $courseMaterial, $attribute)
    {
        $user = $token->getUser();

        if ($user->isTeacher()) {
            foreach ($courseMaterial->getCourse()->getTeachers() as $teacher) {
                if ($teacher->getId() == $user->getTeacher()->getId()) {
                    return true;
                }
            }
        }

        return false;
    }
}
