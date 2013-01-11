<?php
namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Zerebral\CommonBundle\Form\DataTransformer\TimeTransformer;
use Zerebral\FrontendBundle\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

class StudentAttendanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('status', 'choice', array(
            'choices' => array('present' => 'present', 'tardy' => 'tardy', 'excused' => 'excused')
        ));
        $builder->add('comment', 'text', array('max_length' => 200));
        $builder->add('studentId', 'hidden');
        $builder->add('attendanceId', 'hidden');
    }


    public function getName()
    {
        return 'student_attendance';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Attendance\StudentAttendance',

            )
        );
    }
}
