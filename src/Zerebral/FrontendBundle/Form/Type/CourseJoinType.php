<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\BusinessBundle\Model\Course\Discipline;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CourseJoinType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('accessCode', 'text', array(
            'required' => false,
            'constraints' => array(
                new \Symfony\Component\Validator\Constraints\NotBlank(),
                new \Zerebral\BusinessBundle\Validator\Constraints\AccessCode(array(
                    'message' => 'Course not found'
                )),
            )
         ));
    }

    public function getName()
    {
        return 'course_join';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Course\Course',
                'validation_groups' => array('accept_invite'),
            )
        );
    }
}
