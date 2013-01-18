<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Zerebral\CommonBundle\Form\DataTransformer\TimeTransformer;
use Zerebral\FrontendBundle\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

class MessageType extends AbstractType
{
    protected $fileStorage;

    public function setFileStorage($fileStorage)
    {
        $this->fileStorage = $fileStorage;
    }

    public function getFileStorage()
    {
        return $this->fileStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subject', 'text', array('required' => true, 'max_length' => 250));
        $builder->add('body', 'textarea', array('required' => true));
        $builder->add('toName', 'text', array('required' => false));
        $builder->add('to_id', 'hidden', array('required' => false));
        $builder->add('subject', 'text', array('required' => false));

        $builder->add(
            'files',
            'collection',
            array(
                'type' => new \Zerebral\FrontendBundle\Form\Type\FileType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options' => array('storage' => $this->getFileStorage(), 'error_bubbling' => true)
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