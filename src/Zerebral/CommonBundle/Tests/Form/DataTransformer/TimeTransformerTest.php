<?php

namespace Zerebral\CommonBundle\Tests\Form\DataTransformer;

class TimeTransformerTest extends \Zerebral\CommonBundle\Tests\TestCase
{
    public function testReversTransform()
    {
        $transformer = new \Zerebral\CommonBundle\Form\DataTransformer\TimeTransformer();

        $this->assertEquals('00:00', $transformer->reverseTransform('12:00 AM'));
        $this->assertEquals('00:15', $transformer->reverseTransform('12:15 AM'));
        $this->assertEquals('01:15', $transformer->reverseTransform('01:15 AM'));
        $this->assertEquals('12:15', $transformer->reverseTransform('12:15 PM'));
        $this->assertEquals('23:15', $transformer->reverseTransform('11:15 PM'));
    }

    public function testTransform()
    {
        $transformer = new \Zerebral\CommonBundle\Form\DataTransformer\TimeTransformer();

        $this->assertEquals('', $transformer->transform('00:00'));
        $this->assertEquals('12:15 AM', $transformer->transform('00:15'));
        $this->assertEquals('01:15 AM', $transformer->transform('01:15'));
        $this->assertEquals('12:15 PM', $transformer->transform('12:15'));
        $this->assertEquals('11:15 PM', $transformer->transform('23:15'));
    }
}
