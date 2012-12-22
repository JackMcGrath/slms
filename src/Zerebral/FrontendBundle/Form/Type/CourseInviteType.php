<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\DataTransformer\ArrayToTextTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Constraint;

class CourseInviteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('emails', 'textarea', array(
            'required' => false,
            'constraints' => array(
                new Constraint\Count(array(
                    'min' => 1,
                    'minMessage' => 'Please, specify emails list',
                )),
                new Constraint\All(array(
                    new Constraint\Email(array(
                        'message' => '{{ value }} is not valid email'
                    ))
                ))
            )
        ));

        $builder->get('emails')->addModelTransformer(new ArrayToTextTransformer("\n"));
    }

    public function getName()
    {
        return 'course_invite';
    }
}
