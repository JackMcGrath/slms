<?php

namespace Zerebral\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class TimeTransformer implements DataTransformerInterface
{
    public function reverseTransform($value)
    {
        if (preg_match('/^\d{2}:\d{2}\s(AM|PM)$/is', $value, $matches)) {
            return date("H:i", strtotime(strtolower($value)));
        }
        return $value;
    }

    public function transform($value)
    {
        if (preg_match('/^\d{2}:\d{2}$/is', $value, $matches)) {
            if ($value == '00:00')
                return '';

            return strtoupper(date("h:i a", strtotime($value)));
        }
        return $value;
    }
}
