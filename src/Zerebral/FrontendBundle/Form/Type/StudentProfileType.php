<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StudentProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserProfileType());
        $builder->add('bio', 'textarea', array('required' => false, 'max_length' => 160));
        $builder->add('activities', 'textarea', array('required' => false));
        $builder->add('interests', 'textarea', array('required' => false));
    }

    public function getName()
    {
        return 'profile';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\User\Student',
                'cascade_validation' => true,
                'validation_groups' => array('profile')
            )
        );
    }
}
