<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zerebral\BusinessBundle\Model\User\User;

class UserProfileType extends AbstractType
{
    protected $fileStorage;

    public function setFileStorage($fileStorage) {
        $this->fileStorage = $fileStorage;
    }

    public function getFileStorage() {
        return $this->fileStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birthday', 'date', array('required' => false, 'input' => 'datetime', 'widget' => 'single_text', 'format' => 'MM/dd/yyyy'));
        $builder->add('gender', 'choice', array('required' => false, 'choices' => array('male' => 'Male', 'female' => 'Female')));
        $builder->add('avatar', new \Zerebral\FrontendBundle\Form\Type\FileType(), array(
            'storage' => $this->getFileStorage(),
            'by_reference' => true
        ));
    }

    public function getName()
    {
        return 'user';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('fileQuery'));
        $resolver->setDefaults(array(
            'data_class' => 'Zerebral\BusinessBundle\Model\User\User',
            'validation_groups' => array('profile')
        ));
    }
}
