<?php

namespace Zerebral\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class TimeTransformer implements DataTransformerInterface
{
    public function reverseTransform($value)
    {
        if (preg_match('/(\d{2}):(\d{2})\s(AM|PM)/is', $value, $matches)) {
            list($time, $hour, $minutes, $meridian) = $matches;
            if (strtoupper($meridian) == 'PM') {
                $hour += 12;
            }
            return $hour . ":" . $minutes;
        }
        return $value;
    }

    public function transform($value)
    {
        return $value;
    }
}
