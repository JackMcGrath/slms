<?php

namespace Zerebral\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

use Symfony\Component\Form\Util\PropertyPath;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class OptionalModelTransformer implements DataTransformerInterface
{
    /**
     * @var \Closure
     */
    private $createModel;

    /**
     * @var PropertyPath
     */
    private $labelPropertyPath;

    /**
     * @var PropertyPath
     */
    private $valuePropertyPath;

    /**
     * @var ChoiceList
     */
    private $choiceList;

    public function __construct(\Closure $createModel, $labelProperty = 'name', $valueProperty = 'id', ChoiceList $choiceList)
    {
        $this->createModel = $createModel;
        $this->labelPropertyPath = new PropertyPath($labelProperty);
        $this->valuePropertyPath = new PropertyPath($valueProperty);
        $this->choiceList = $choiceList;

    }

    public function reverseTransform($value)
    {
        // if form contains text
        if (isset($value['name']) && !empty($value['name'])) {
            return call_user_func($this->createModel, $value['name']);
        }

        if (isset($value['model']) && !empty($value['model'])) {
            return $value['model'];
        }

        return null;
    }

    public function transform($model)
    {
        if (is_null($model)) {
            return array(
                'model' => null,
                'name' => '',
            );
        }

        return array(
            'model' => !$model->isNew() ? $this->getChoice($this->valuePropertyPath->getValue($model)) : null,
            'name' => $model->isNew() ? $this->labelPropertyPath->getValue($model) : '',
        );
    }

    private function getChoice($value)
    {
        return current($this->choiceList->getChoicesForValues(array($value)));
    }
}
