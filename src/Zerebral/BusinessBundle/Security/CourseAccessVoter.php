<?php

namespace Zerebral\BusinessBundle\Security;

use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Zerebral\BusinessBundle\Model\User\User;

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
    public function isGranted(TokenInterface $token, $course, $attribute)
    {
        $user = $token->getUser();

        if ($user->getRole() == User::ROLE_TEACHER) {
            foreach ($course->getTeachers() as $teacher) {
                if ($teacher->getId() == $user->getTeacher()->getId()) {
                    return true;
                }
            }
            return false;
        }

        if ($user->getRole() == User::ROLE_STUDENT) {
            if (strtoupper($attribute) != 'VIEW') {
                return false;
            }

            foreach ($course->getCourseStudents() as $courseStudent) {
                if ($courseStudent->getStudentId() == $user->getStudent()->getId()) {
                    return true;
                }
            }
            return false;
        }

        if ($user->getRole() == User::ROLE_GUARDIAN) {
            return true; //all access check in controller
        }

        return false;
    }
}
