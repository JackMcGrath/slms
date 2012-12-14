<?php
namespace Zerebral\FrontendBundle\Form\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SignupConfirmPasswordValidator extends ConstraintValidator
{
    /**
     * Check that password and confirmation is equal
     * @param mixed $form
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value != $this->context->getRoot()->get('plainPassword')->getData()) {
            $this->context->addViolation($constraint->message);
        }
    }
}
