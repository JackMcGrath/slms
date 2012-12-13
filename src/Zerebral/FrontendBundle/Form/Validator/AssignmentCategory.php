<?php

namespace Zerebral\FrontendBundle\Form\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AssignmentCategory extends Constraint
{
    public $message = 'Category is required.';
}