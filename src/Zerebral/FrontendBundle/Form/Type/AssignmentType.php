<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zerebral\FrontendBundle\Form\Validator\AssignmentCategory;
use Symfony\Component\Validator\Constraints\Collection;

class AssignmentType extends AbstractType
{

    protected $teacher;

    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false));
        $builder->add('description', 'textarea', array('required' => false));

        $teacher = $this->teacher;

        $builder->add('assignment_category', new OptionalModelType(), array(
            'dropdown' => array(
                'empty_value' => "What's assignment category?",
                'empty_data' => 0,
                'invalid_message' => 'Category is required.',
            ),
            'class' => 'Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory',
            'property' => 'name',
            'required' => false,

            'callback' => function($model, $value) use ($teacher) {
                $model->setTeacher($teacher);
                $model->setName($value);
                return $model;
            }
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
        $collectionConstraint = array(
            'assignment_category' => new AssignmentCategory(),
        );

        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Assignment\Assignment',
                'constraints' => $collectionConstraint
            )
        );
    }
}