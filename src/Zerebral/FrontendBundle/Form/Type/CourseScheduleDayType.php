<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\DataTransformer\TimeTransformer;
use Zerebral\BusinessBundle\Model\Course\Discipline;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CourseScheduleDayType extends AbstractType
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
        $builder->add('week_day', 'choice', array(
                'required' => false,
                'choices' => array(
                    'Monday' => 'Monday',
                    'Tuesday' => 'Tuesday',
                    'Wednesday' => 'Wednesday',
                    'Thursday' => 'Thursday',
                    'Friday' => 'Friday',
                    'Saturday' => 'Saturday',
                    'Sunday' =>'Sunday'
                ),
                'empty_value' => false,
        ));
        $builder->add('time_from', 'time', array(
                'required' => false,
                'input'  => 'datetime',
                'widget' => 'single_text',
        ));
        $builder->add('time_to', 'time', array(
                'required' => false,
                'input'  => 'datetime',
                'widget' => 'single_text',
        ));
        $builder->get('time_from')->addViewTransformer(new TimeTransformer());
        $builder->get('time_to')->addViewTransformer(new TimeTransformer());
    }

    public function getName()
    {
        return 'course_schedule_day';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Course\CourseScheduleDay',
            ));
    }
}
