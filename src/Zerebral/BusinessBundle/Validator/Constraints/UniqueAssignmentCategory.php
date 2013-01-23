<?php

namespace Zerebral\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueAssignmentCategory extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Assignment category already exists with such name';

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
