<?php

namespace Zerebral\FrontendBundle\Form\Type;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\File\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

class MessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject', 'text', array('required' => true, 'max_length' => 250));
        $builder->add('body', 'textarea', array('required' => true));
        $builder->add('to', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\User\User',
            'property' => 'full_name',
            'required' => false,
            'empty_value' => "To",
            'empty_data' => 0,
            'invalid_message' => 'Recipient is required.',
        ));
//        $builder->add('toId', 'choice', array('required' => false));
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