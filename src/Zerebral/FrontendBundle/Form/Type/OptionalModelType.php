<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\FrontendBundle\Form\DataTransformer\StringToModelTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Bridge\Propel1\Form\ChoiceList\ModelChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Propel1\Form\Type\ModelType;

class OptionalModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('input', 'text', array('required' => false));
        $builder->add('dropdown', 'model', array(
                'class' => $options['class'],
                'property' => $options['property'],
                'required' => false,
                'empty_value' => $options['dropdown']['empty_value'],
//                'empty_data' => $options['dropdown']['empty_data'],
//                'invalid_message' => $options['dropdown']['invalid_message'],
        ));

        $builder->addViewTransformer(new ReversedTransformer(
            new StringToModelTransformer($options['class'], $options['callback'])
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'dropdown' => array(),
            'input' => array(),
            'class' => null,
            'property' => 'name',
            'callback' => null
        ));
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