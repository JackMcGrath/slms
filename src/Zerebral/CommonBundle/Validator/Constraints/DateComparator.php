<?php

namespace Zerebral\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * @Annotation
 *
 * @api
 */
class DateComparator extends Constraint
{
    public $message = '{{ target_field }} should be {{ comparator }} {{ source_field }}';

    public $targetField;

    public $sourceField;

    public $comparator = 'greater';

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (empty($this->targetField)) {
            throw new ConstraintDefinitionException("You should specify target field");
        }

        if (empty($this->sourceField)) {
            throw new ConstraintDefinitionException("You should specify source field");
        }

        $allowedComparators = array('greater', 'greater_or_equals', 'equals','not_equals', 'less', 'less_or_equals');
        if (!in_array($this->comparator, $allowedComparators)) {
            throw new ConstraintDefinitionException("Comparator could be only " . join(', ', $allowedComparators));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('targetField', 'sourceField');
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
