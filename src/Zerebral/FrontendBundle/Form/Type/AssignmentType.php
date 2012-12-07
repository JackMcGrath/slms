<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zerebral\BusinessBundle\Model\Course\Course;

class AssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false));
        $builder->add('description', 'textarea', array('required' => false));

        $builder->add('assignment_category', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory',
            'property' => 'name',
            'required' => false,
            'empty_value' => "What's assignment category?",
            'empty_data' => 0,
            'invalid_message' => 'Category is required.',
//          'empty_disabled' => true,
        ));

        $builder->add('course', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\Course\Course',
            'property' => 'name',
            'required' => false,
            'empty_value' => "What's course?",
            'empty_data' => 0,
            'invalid_message' => 'Course is required.',
//          'empty_disabled' => true,
        ));

        $builder->add('max_points', 'text', array('required' => false, 'data' => 100,));
        $builder->add('due_at_date', 'text', array('required' => false));
        $builder->add('due_at_time', 'text', array('required' => false));

//        $builder->add('attachment', 'file');
    }

    public function getName()
    {
        return 'assignment';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Assignment\Assignment',
            )
        );
    }
}