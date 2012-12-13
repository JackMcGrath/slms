<?php

namespace Zerebral\FrontendBundle\Form\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for assignment_category field
 */
class AssignmentCategoryValidator extends ConstraintValidator
{

    /**
     * Check if one of field of complex form field not empty
     * @param mixed $form
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($form, Constraint $constraint)
    {
        $category = $form->getAssignmentCategory();
        if (!$category) {
            $this->context->addViolation($constraint->message);
        }
    }
}