<?php

namespace Zerebral\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryPeer;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory;

class UniqueAssignmentCategoryValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     * @param AssignmentCategory $category
     * @param UniqueAssignmentCategory $constraint
     */
    public function validate($category, Constraint $constraint)
    {
        $existCategory = AssignmentCategoryQuery::create()
                ->where('TRIM(LOWER(' . AssignmentCategoryPeer::NAME . ')) = ?', trim(strtolower($category->getName())))
                ->where('(' . AssignmentCategoryPeer::TEACHER_ID . ' = ? OR ' . AssignmentCategoryPeer::TEACHER_ID . ' IS NULL)', $category->getTeacherId() ?: '')
                ->findOne();

        if (!empty($existCategory) && $existCategory->getId() != $category->getId()) {
            $this->context->addViolation($constraint->message);
        }
    }
}
