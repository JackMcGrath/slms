<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

class AssignmentType extends AbstractType
{

    protected $teacher;

    /**
     * Set teacher model to class instance for using it in callback function
     * @param \Zerebral\BusinessBundle\Model\User\Teacher $teacher
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false));
        $builder->add('description', 'textarea', array('required' => false));

        $builder->add('assignmentCategory', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory',
            'property' => 'name',
            'required' => false,
            'empty_value' => "What's assignment category?",
            'empty_data' => 0,
            'invalid_message' => 'Category is required',
        ));

        $builder->add('max_points', 'text', array('required' => false, 'data' => 100,));
        $builder->add('due_at_date', 'text', array('required' => false));
        $builder->add('due_at_time', 'text', array('required' => false));
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