<?php

namespace Zerebral\BusinessBundle\Security;

use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Zerebral\BusinessBundle\Model\User\User;
use Zerebral\BusinessBundle\Model\File\File;

class FileAccessVoter extends \Zerebral\CommonBundle\Security\ModelAccessVoter
{
    protected function getSupportedAttributes()
    {
        return array('DOWNLOAD');
    }

    protected function getModelClass()
    {
        return '\Zerebral\BusinessBundle\Model\File\File';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param \Zerebral\BusinessBundle\Model\File\File $file
     * @param $attribute
     */
    public function isGranted(TokenInterface $token, $file, $attribute)
    {
        if ($file->getCourseMaterials()->count() > 0) {
            $course = $file->getCourseMaterials()->getFirst()->getCourse();
            $voter = new CourseAccessVoter();
            return $voter->isGranted($token, $course, 'VIEW');
        } elseif ($file->getAssignments()->count() > 0) {
            $assignment = $file->getAssignments()->getFirst();
            $voter = new AssignmentAccessVoter();
            return $voter->isGranted($token, $assignment, 'VIEW');
        } elseif ($file->getStudentAssignments()->count() > 0) {
            $assignment = $file->getStudentAssignments()->getFirst()->getAssignment();
            $voter = new AssignmentAccessVoter();
            return $voter->isGranted($token, $assignment, 'VIEW');
        } elseif ($file->getMessages()->count() > 0) {
            // TODO: put MessageAccessVoter
            return true;
        }
    }
}
