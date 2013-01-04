<?php

namespace Zerebral\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Util\PropertyPath;

class DateCompareValidator extends CompareValidator
{
    /**
     * {@inheritDoc}
     */
    protected function getPropertyValue($object, $field)
    {
        $path = new PropertyPath($field);
        $value = $path->getValue($object);

        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }


}
