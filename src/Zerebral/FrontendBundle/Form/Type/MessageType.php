<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\File\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Zerebral\BusinessBundle\Model\Message\Message;

class MessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject', 'text', array('required' => false, 'max_length' => 250));
        $builder->add('body', 'textarea', array('required' => false));


        $formFactory = $builder->getFormFactory();
        $builder->addEventListener(\Symfony\Component\Form\FormEvents::PRE_SET_DATA, function(\Symfony\Component\Form\FormEvent $event) use ($formFactory) {
            /** @var $message Message */
            $message = $event->getData();
            if (!$message->getThreadId()) {
                $field = $formFactory->createNamed('to','model', $message->getTo(), array(
                    'class' => 'Zerebral\BusinessBundle\Model\User\User',
                    'property' => 'full_name',
                    'required' => false,
                    'empty_value' => "To",
                    'empty_data' => 0,
                    'invalid_message' => 'Recipient is required.'
                ));

                $event->getForm()->add($field);
            }
        });

        $builder->add('subject', 'text', array('required' => false));

        $builder->add(
            'files',
            'collection',
            array(
                'type' => new FileType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options' => array(
                    'error_bubbling' => true,
                    'folder' => 'message',
                ),
            )
        );
    }

    public function getName()
    {
        return 'message';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Message\Message',
            )
        );
    }
}