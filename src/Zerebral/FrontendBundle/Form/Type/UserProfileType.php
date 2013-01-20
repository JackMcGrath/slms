<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birthday', 'date', array('required' => false, 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'MM/dd/yyyy'));
        $builder->add('gender', 'choice', array('required' => false, 'choices' => array('male' => 'Male', 'female' => 'Female')));
        $builder->add(
            'avatar',
            new \Zerebral\CommonBundle\File\Form\Type\FileType,
            array(
                'by_reference' => true,
                'required' => false,
                'folder' => 'user-avatar'
            )
        );
    }

    public function getName()
    {
        return 'user';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('fileQuery'));
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\User\User',
                'validation_groups' => array('profile')
            )
        );
    }
}
