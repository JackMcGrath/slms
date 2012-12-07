<?php

namespace Zerebral\BusinessBundle\Security;

use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CourseAccessVoter extends \Zerebral\CommonBundle\Security\ModelAccessVoter
{
    protected function getSupportedAttributes()
    {
        return array('EDIT', 'VIEW', 'DELETE', 'ADD_ASSIGNMENT');
    }

    protected function getModelClass()
    {
        return '\Zerebral\BusinessBundle\Model\Course\Course';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @param $attribute
     */
    protected function isGranted(TokenInterface $token, $course, $attribute)
    {
        return $course->getTeacher()->getUser()->getId() == $token->getUser()->getId();
    }
}
