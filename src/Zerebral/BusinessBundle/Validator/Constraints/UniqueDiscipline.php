<?php

namespace Zerebral\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueDiscipline extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Discipline already exists with such name';

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
