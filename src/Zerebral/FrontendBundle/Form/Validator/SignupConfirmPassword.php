<?php

namespace Zerebral\FrontendBundle\Form\Validator;

use Symfony\Component\Validator\Constraint;

class SignupConfirmPassword extends Constraint
{
    public $message = 'Looks like a mistype. The passwords must match.';
}