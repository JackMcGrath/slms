<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zerebral\BusinessBundle\Model\Course\Course;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false));
        $builder->add('description', 'textarea', array('required' => false));
        $builder->add('discipline', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\Course\Discipline',
            'property' => 'name',
            'required' => false,
            'empty_value' => "What's course subject area?",
            'empty_data' => 0,
//            'empty_disabled' => true,
        ));
        $builder->add('grade_level', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\Course\GradeLevel',
            'property' => 'name',
            'required' => false,
            'empty_value' => "Which grade level do you plan to use?",
            'empty_data' => 0,
        ));
    }

    public function getName()
    {
        return 'course';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zerebral\BusinessBundle\Model\Course\Course',
        ));
    }
}
