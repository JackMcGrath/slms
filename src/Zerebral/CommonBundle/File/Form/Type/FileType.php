<?php

namespace Zerebral\CommonBundle\File\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zerebral\BusinessBundle\Model\File\File;


class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('temporaryFile', 'hidden');
        $builder->add('name', 'hidden');
        $builder->add('mimeType', 'hidden');

        $builder->add('uploadedFile', 'file', array('required' => false));
        $builder->add('description', $options['has_description'] ? 'text' : 'hidden', array('required' => false));

        $builder->addEventListener(\Symfony\Component\Form\FormEvents::POST_BIND, function(\Symfony\Component\Form\FormEvent $event) {
            $folder = $event->getForm()->getConfig()->getOption('folder');
            /** @var $file File */
            $file = $event->getData();
            if (!empty($file)) {
                $file->setFolder($folder);
            }
        });
    }

    public function getName()
    {
        return 'custom_file';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\File\File',
                'error_mapping' => array(
                    'name' => 'uploadedFile'
                ),
                'folder' => '',
                'has_description' => false,
            )
        );
    }
}