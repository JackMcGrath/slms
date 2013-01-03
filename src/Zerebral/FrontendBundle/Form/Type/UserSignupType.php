<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zerebral\BusinessBundle\Model\User\User;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Collection;

class UserSignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', array('required' => false, 'max_length' => 100));
        $builder->add('lastName', 'text', array('required' => false, 'max_length' => 100));
        $builder->add('email', 'email', array('required' => false, 'max_length' => 100));
        $builder->add('plainPassword', 'password', array('required' => false));
        $builder->add('passwordConfirmation', 'password', array('required' => false));
        $builder->add('role', 'choice', array(
            'choices' => array(
                User::ROLE_TEACHER => 'Teacher',
                User::ROLE_STUDENT => 'Student',
                'parent' => 'Parent',
            ),
            'expanded' => true,
            'multiple' => false,
            'data' => User::ROLE_TEACHER,
            'required' => false,
        ));
    }

    public function getName()
    {
        return 'user_signup';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zerebral\BusinessBundle\Model\User\User',
            'validation_groups' => array('signup'),
            'error_mapping' => array(
                '.' => 'email'
            )
        ));
    }
}
