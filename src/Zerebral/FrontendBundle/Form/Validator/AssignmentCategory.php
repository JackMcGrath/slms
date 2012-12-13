<?php

namespace Zerebral\FrontendBundle\Form\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Validator for assignment_category field
 */
class AssignmentCategory extends Constraint
{
    public $message = 'Category is required.';
}