<?php

namespace Zerebral\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AccessCodeValidator extends ConstraintValidator
{

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $courses = \Zerebral\BusinessBundle\Model\Course\CourseQuery::create()->findByAccessCode($value)->count();

        if ($courses == 0) {
            $this->context->addViolation($constraint->message, array('{{ value }}' => $value));
        }
    }
}
