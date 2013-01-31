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
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'cascade_validation' => true,
            'error_bubbling' => false,
            'options' => array(
                'class' => 'Zerebral\BusinessBundle\Model\User\User',
                'required' => true
            )
        ));



//                $field = $formFactory->createNamed('to', 'model', $message->getTo(), array(
//                    'class' => 'Zerebral\BusinessBundle\Model\User\User',
//                    'property' => 'full_name',
//                    'required' => false,
//                    'empty_value' => "To",
//                    'empty_data' => 0,
//                    'invalid_message' => 'Recipient is required.'
//                ));

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