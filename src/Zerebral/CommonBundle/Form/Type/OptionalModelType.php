<?php

namespace Zerebral\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\DataTransformer\OptionalModelTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

//use Zerebral\CommonBundle\Form\Validator\AssignmentCategory;

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
        $choiceList = new ObjectChoiceList(
            $options['choices'],
            $options['label_property'],
            array(),
            null,
            $options['value_property']
        );

        $builder->add(
            'name',
            'text',
            array(
                'required' => false,
                'error_bubbling' => true,
            )
        );
        $builder->add(
            'model',
            'choice',
            array(
                'required' => false,
                'error_bubbling' => true,
                'expanded' => false,
                'multiple' => false,
                'choice_list' => $choiceList,
                'empty_value' => $options['empty_value'],
                'empty_data' => '',
                'invalid_message' => $options['invalid_message'],
            )
        );

        $builder->addModelTransformer(new OptionalModelTransformer($options['create_model'], $options['label_property'], $options['value_property'], $choiceList));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => array(),
                'label_property' => 'name',
                'value_property' => 'id',
                'create_model' => null,
                'compound' => true,
                'empty_value' => null,
                'error_bubbling' => false,
                'data_class' => null,
                'by_reference' => false,
                'allow_create' => false,
                'cascade_validation' => true,
                'invalid_message' => 'The value is not valid',

                'create_new_label' => 'Create new',
                'choose_exists_label' => 'Choose exists',
                'placeholder' => '',
            )
        );
        $resolver->setRequired(array('create_model', 'choices'));
        $resolver->setAllowedTypes(
            array(
                'create_model' => 'Closure',
            )
        );
    }

//    public function finishView()
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['create_new_label'] = $options['create_new_label'];
        $view->vars['choose_exists_label'] = $options['choose_exists_label'];
        $view->vars['placeholder'] = $options['placeholder'];
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