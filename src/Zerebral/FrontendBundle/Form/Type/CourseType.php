<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\BusinessBundle\Model\Course\Discipline;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CourseType extends AbstractType
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
        $builder->add('discipline', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\Course\Discipline',
            'property' => 'name',
            'required' => false,
            'empty_value' => "Course subject area...",
            'empty_data' => 0,
            'invalid_message' => 'Course subject is required.',
        ));

        $builder->add('gradeLevel', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\Course\GradeLevel',
            'property' => 'name',
            'required' => false,
            'empty_value' => "Grade level...",
            'empty_data' => 0,
            'invalid_message' => 'Grade level is required.',
        ));

        $builder->add('courseScheduleDays', 'collection', array(
            'type'   => new CourseScheduleDayType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'options'  => array(
                'required'  => false,
            ),
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
