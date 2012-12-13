<?php

namespace Zerebral\FrontendBundle\Form\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AssignmentCategoryValidator extends ConstraintValidator{

    public function validate($form, Constraint $constraint)
    {
        $category = $form->getAssignmentCategory();
        if(!$category){
            $this->context->addViolationAtSubPath('assignment_category', $constraint->message);
        }
    }
}