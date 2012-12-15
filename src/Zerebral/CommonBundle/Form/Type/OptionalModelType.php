<?php

namespace Zerebral\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\DataTransformer\OptionalModelTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;

use Zerebral\CommonBundle\Form\Validator\AssignmentCategory;

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

        $builder->add('name', 'text', array('required' => false));
        $builder->add('model', 'model',
            array(
                'class' => $options['class'],
                'property' => $options['property'],
                'required' => false,
                'empty_value' => $options['empty_value'],
                'empty_data' => $options['empty_data'],
                'error_bubbling' => true,
//                'invalid_message' => 'In',
//                'invalid_message' => $options['dropdown']['invalid_message'],
            )
        );

        $builder->addViewTransformer(new OptionalModelTransformer($options['class'], $options['property']), true);
//        $builder->addModelTransformer(new OptionalModelTransformer($options['class'], $options['property']), true);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => null,
                'property' => 'name',

                'buildModel' => null,

                'compound' => true,
                'empty_value' => null,
                'empty_data' => '',
                'error_bubbling' => false,
                'data_class'     => null,
                'by_reference'      => false,

                'allow_create' => false,
                'cascade_validation' => false,
            )
        );
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'optional_model';
    }

}