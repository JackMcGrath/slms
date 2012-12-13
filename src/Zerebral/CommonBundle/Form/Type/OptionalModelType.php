<?php

namespace Zerebral\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\DataTransformer\OptionalToModelTransformer;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class OptionalModelType extends AbstractType
{
    /**
     * Create complex form field (model dropdown and create new input field)
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('input', 'text', array('required' => false));
        $builder->add(
            'dropdown',
            'model',
            array(
                'class' => $options['class'],
                'property' => $options['property'],
                'required' => false,
                'empty_value' => $options['dropdown']['empty_value'],
//                'empty_data' => $options['dropdown']['empty_data'],
//                'invalid_message' => $options['dropdown']['invalid_message'],
            )
        );

        $builder->addViewTransformer(
            new ReversedTransformer(
                new OptionalToModelTransformer($options['class'], $options['buildModel'])
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'dropdown' => array(),
                'input' => array(),
                'class' => null,
                'property' => 'name',
                'buildModel' => null
            )
        );
    }

    public function getParent()
    {
        return 'field';
    }

    public function getName()
    {
        return 'choice_optional';
    }

}