<?php

namespace Zerebral\BusinessBundle\Security;

use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Zerebral\BusinessBundle\Model\User\User;

class AssignmentAccessVoter extends \Zerebral\CommonBundle\Security\ModelAccessVoter
{
    private $courseAccessVoter = null;

    /**
     * @return CourseAccessVoter
     */
    private function getCourseAccessVoter()
    {
        if (is_null($this->courseAccessVoter)) {
            $this->courseAccessVoter = new CourseAccessVoter();
        }

        return $this->courseAccessVoter;
    }

    protected function getSupportedAttributes()
    {
        return array('EDIT', 'VIEW', 'DELETE', 'UPLOAD_SOLUTION');
    }

    protected function getModelClass()
    {
        return '\Zerebral\BusinessBundle\Model\Assignment\Assignment';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Zerebral\BusinessBundle\Model\Assignment\Assignment $assignment
     * @param $attribute
     */
    public function isGranted(TokenInterface $token, $assignment, $attribute)
    {
        $user = $token->getUser();

        if (strtoupper($attribute) == 'UPLOAD_SOLUTION' && $user->getRole() != User::ROLE_STUDENT) {
            return false;
        }

        return $this->getCourseAccessVoter()->isGranted($token, $assignment->getCourse(), $attribute);
    }
}
