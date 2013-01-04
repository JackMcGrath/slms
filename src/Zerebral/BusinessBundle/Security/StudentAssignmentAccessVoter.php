<?php

namespace Zerebral\BusinessBundle\Security;

use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Zerebral\BusinessBundle\Model\User\User;

class StudentAssignmentAccessVoter extends \Zerebral\CommonBundle\Security\ModelAccessVoter
{
    /**
     * @var AssignmentAccessVoter
     */
    private $assignmentAccessVoter = null;

    /**
     * @return AssignmentAccessVoter
     */
    private function getAssignmentAccessVoter()
    {
        if (is_null($this->assignmentAccessVoter)) {
            $this->assignmentAccessVoter = new AssignmentAccessVoter();
        }

        return $this->assignmentAccessVoter;
    }

    protected function getSupportedAttributes()
    {
        return array('EDIT', 'VIEW', 'DELETE', 'UPLOAD', 'SUBMIT');
    }

    protected function getModelClass()
    {
        return '\Zerebral\BusinessBundle\Model\Assignment\StudentAssignment';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Zerebral\BusinessBundle\Model\Assignment\StudentAssignment $assignment
     * @param $attribute
     *
     * @return bool
     */
    public function isGranted(TokenInterface $token, $studentAssignment, $attribute)
    {
        $user = $token->getUser();

        if ($user->getRole() == User::ROLE_TEACHER) {
            if (strtoupper($attribute) != 'UPLOAD' && strtoupper($attribute) != 'SUBMIT') {
                return $this->getAssignmentAccessVoter()->isGranted($token, $studentAssignment->getAssignment(), $attribute);
            }
            return false;
        }

        if ($user->getRole() == User::ROLE_STUDENT) {
            // student can't modify submitted solutions
            if (strtoupper($attribute) != 'VIEW' && $studentAssignment->getIsSubmitted()) {
                return false;
            }

            return $this->getAssignmentAccessVoter()->isGranted($token, $studentAssignment->getAssignment(), 'VIEW');
        }

        return false;
    }
}
