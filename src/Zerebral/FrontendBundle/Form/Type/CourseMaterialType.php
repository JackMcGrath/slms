<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\File\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CourseMaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', new FileType(), array(
            'by_reference' => true,
            'folder' => 'course-material'
        ));

        $builder->add('description', 'text', array('max_length' => 255));
    }

    public function getName()
    {
        return 'course_material';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Material\CourseMaterial',
                'error_mapping' => array(
                    'file' => 'description',
                )
            )
        );
    }
}
