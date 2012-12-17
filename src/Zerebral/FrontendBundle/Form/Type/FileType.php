<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

use Zerebral\FrontendBundle\Form\EventListener\FileSubscriber;

class FileType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $fileSubscriber = new FileSubscriber($builder);
        $builder->addEventSubscriber($fileSubscriber);
        $builder->add('uploadedFile', 'file', array('required' => false));

    }

    public function getName() {
        return 'file';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setOptional(array('storage'));
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\File\File',
                'storage' => 'local'
            )
        );
    }
}