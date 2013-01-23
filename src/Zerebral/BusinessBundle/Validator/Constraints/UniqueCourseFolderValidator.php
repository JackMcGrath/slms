<?php

namespace Zerebral\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Zerebral\BusinessBundle\Model\Material\CourseFolder;
use Zerebral\BusinessBundle\Model\Material\CourseFolderPeer;
use Zerebral\BusinessBundle\Model\Material\CourseFolderQuery;

class UniqueCourseFolderValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     * @param CourseFolder $folder
     * @param UniqueCourseFolder $constraint
     */
    public function validate($folder, Constraint $constraint)
    {
        $existFolder = CourseFolderQuery::create()
                ->where('TRIM(LOWER(' . CourseFolderPeer::NAME . ')) = ?', trim(strtolower($folder->getName())))
                ->where(CourseFolderPeer::COURSE_ID . ' = ?', $folder->getCourseId())
                ->findOne();

        if (!empty($existFolder) && $existFolder->getId() != $folder->getId()) {
            $this->context->addViolation($constraint->message);
        }
    }
}
