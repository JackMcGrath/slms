<?php

namespace Zerebral\CommonBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Constraint which allow to compare 2 object fields
 * 
 * Accept to 3 params:
 *  - target field: what field we will validate
 *  - source field: what field we will compare with
 *  - comparator: how we will compare (supported values: greater, greater_or_equals, equals,not_equals, less, less_or_equals)
 *
 * NOTE: validation errors would be attached to target field, NOT to class
 *  
 * @Annotation
 *
 * @api
 */
class Compare extends Constraint
{
    /**
     * Error message
     * @var string
     */
    public $message = '{{ target_field }} should be {{ comparator }} {{ source_field }}';

    /**
     * Target field path
     * @var string
     */
    public $targetField;

    /**
     * Source field path
     * @var string
     */
    public $sourceField;

    /**
     * Comparator
     * @var string
     */
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
