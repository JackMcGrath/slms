<?php

namespace Zerebral\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\Util\PropertyPath;


/**
 * Compare constraint validator
 */
class CompareValidator extends ConstraintValidator
{

    /**
     * {@inheritDoc}
     *
     * @param mixed $object
     * @param \Zerebral\CommonBundle\Validator\Constraints\Compare $constraint
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

        if (!$this->compare($targetValue, $sourceValue, $constraint->comparator)) {
            $this->context->addViolationAtSubPath($constraint->targetField, $constraint->message, array(
                '{{ target_field }}' => $constraint->targetField,
                '{{ source_field }}' => $constraint->sourceField,
                '{{ comparator }}' => str_replace('_', ' ', $constraint->comparator),
            ));
        }
    }

    /**
     * Get object property value by property path
     *
     * @param object $object
     * @param string $field property path
     * @return string
     */
    protected function getPropertyValue($object, $field)
    {
        $path = new PropertyPath($field);
        return $path->getValue($object);
    }

    /**
     * Compare values
     *
     * @param string|int $target
     * @param string|int $source
     * @param string $comparator
     * @return bool
     */
    private function compare($target, $source, $comparator = 'greater')
    {
        switch($comparator) {
            case 'greater': return $target > $source;
            case 'greater_or_equals': return $target >= $source;
            case 'equals': return $target == $source;
            case 'less_or_equals': return $target <= $source;
            case 'less': return $target <= $source;
            case 'not_equals': return $target != $source;
        }

        return false;
    }

}
