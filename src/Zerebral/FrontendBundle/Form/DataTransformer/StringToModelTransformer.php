<?php

namespace Zerebral\FrontendBundle\Form\DataTransformer;
use Symfony\Component\Form\DataTransformerInterface;

class StringToModelTransformer implements DataTransformerInterface
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
        if(isset($value['input']) && !is_null($value['input'])){
            return call_user_func($this->callback, new $this->className, $value['input']);
        }else if(isset($value['dropdown']) && !is_null($value['dropdown'])){
            return $value['dropdown'];
        }
    }

    public function reverseTransform($value)
    {
       return $value;
    }
}
