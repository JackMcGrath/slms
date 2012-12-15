<?php

namespace Zerebral\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class OptionalModelTransformer implements DataTransformerInterface
{
    private $className;

    /**
     * @var \Closure
     */
    private $buildModel;

    private $property = null;

    public function __construct($className, $property)
    {
        $this->className = (string)$className;
        $this->property = $property;
    }


    public function reverseTransform($value)
    {
        if (isset($value['name']) && !empty($value['name'])) {
            $model = new $this->className;
//            $model->fromArray(array(
//                $this->property => $value['name']
//            ));
            $model->setName($value['name']);
            return $model;
        } else {
            if (isset($value['model']) && !empty($value['model'])) {
                return $value['model'];
            }
        }

        return null;
    }

    public function transform( $value)
    {
        if (is_null($value))
            $value = new $this->className;

        return array(
//            'model' => $value,
            'name' => $value->getName(),
        );
    }
}
