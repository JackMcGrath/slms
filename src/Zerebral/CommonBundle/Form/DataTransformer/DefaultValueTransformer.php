<?php

namespace Zerebral\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DefaultValueTransformer implements DataTransformerInterface
{
    private $defaultValue = '';

    public function __construct($defaultValue = '')
    {
        $this->setDefaultValue($defaultValue);
    }

    public function reverseTransform($value)
    {
        return $value;
    }

    public function transform($value)
    {
        if (empty($value)) {
            return $this->getDefaultValue();
        }

        return $value;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }


}
