<?php

namespace Zerebral\BusinessBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 */
class AccessCode extends Constraint
{
    public $message = 'This value is not a valid access code.';
}
