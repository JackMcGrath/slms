<?php

namespace Zerebral\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Util\PropertyPath;

class DateComparatorValidator extends ConstraintValidator
{

    /**
     * {@inheritDoc}
     */
    public function validate($object, Constraint $constraint)
    {
        $sourceValue = $this->getPropertyValue($object, $constraint->sourceField);
        $targetValue = $this->getPropertyValue($object, $constraint->targetField);



        if (null === $sourceValue || '' === $sourceValue) {
            return;
        }

        if (null === $targetValue || '' === $targetValue) {
            return;
        }

        if (!($sourceValue instanceof \DateTime)) {
            throw new UnexpectedTypeException($sourceValue, 'string');
        }

        if (!($targetValue instanceof \DateTime)) {
            throw new UnexpectedTypeException($targetValue, 'string');
        }

        if (!$this->compare($targetValue->format('Y-m-d H:i:s'), $sourceValue->format('Y-m-d H:i:s'), $constraint->comparator)) {
            $this->context->addViolationAtSubPath($constraint->targetField, $constraint->message, array(
                '{{ target_field }}' => $constraint->targetField,
                '{{ source_field }}' => $constraint->sourceField,
                '{{ comparator }}' => str_replace('_', ' ', $constraint->comparator),
            ));
        }
    }

    private function getPropertyValue($object, $field)
    {
        $path = new PropertyPath($field);
        return $path->getValue($object);
    }

    private function compare($target, $source, $comparator = 'greater')
    {
        switch($comparator) {
            case 'greater': return $target > $source;
            case 'greater_or_equals': return $target >= $source;
            case 'equals': return $target >= $source;
            case 'less_or_equals': return $target <= $source;
            case 'less': return $target <= $source;
            case 'not_equals': return $target != $source;
        }

        return false;
    }

}
