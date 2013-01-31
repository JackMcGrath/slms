<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GuardianProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserProfileType());
        $builder->add('notes', 'textarea', array('required' => false));
    }

    public function getName()
    {
        return 'profile';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\User\Guardian',
                'cascade_validation' => true,
                'validation_groups' => array('profile')
            )
        );
    }
}
