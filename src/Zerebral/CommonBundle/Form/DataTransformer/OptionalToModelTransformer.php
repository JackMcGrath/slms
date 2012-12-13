<?php

namespace Zerebral\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class OptionalToModelTransformer implements DataTransformerInterface
{
    private $className;

    /**
     * @var \Closure
     */
    private $callback;

    public function __construct($className, \Closure $callback = null)
    {
        $this->className = (string)$className;
        $this->callback = $callback;
    }

    public function transform($value)
    {
        if (isset($value['input']) && !empty($value['input'])) {
            return call_user_func($this->buildModel, new $this->className, $value['input']);
        } else {
            if (isset($value['dropdown']) && !empty($value['dropdown'])) {
                return $value['dropdown'];
            }
        }

        return null;
    }

    public function reverseTransform($value)
    {
        return $value;
    }
}
