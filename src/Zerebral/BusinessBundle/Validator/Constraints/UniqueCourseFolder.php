<?php

namespace Zerebral\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueCourseFolder extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Folder already exists with such name';

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
