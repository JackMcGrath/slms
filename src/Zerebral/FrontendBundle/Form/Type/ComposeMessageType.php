<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\File\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Zerebral\BusinessBundle\Model\Message\Message;

class ComposeMessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', new MessageType());
        $builder->add('recipients', 'collection', array(
            'type' => 'model',
            'by_reference' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'cascade_validation' => false,
            'error_bubbling' => false,
            'options' => array(
                'class' => 'Zerebral\BusinessBundle\Model\User\User',
                'required' => true
            )
        ));
    }

    public function getName()
    {
        return 'message_compose';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Message\ComposeMessage',
                'cascade_validation' => true,
            )
        );
    }
}