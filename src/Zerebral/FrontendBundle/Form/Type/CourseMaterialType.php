<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\FrontendBundle\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

class CourseMaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', new \Zerebral\FrontendBundle\Form\Type\FileType(), array(
            'by_reference' => true,
            'storage' => $options['storage'],
        ));

        $builder->add('folderId', 'hidden');
        $builder->add('courseId', 'hidden');
        $builder->add('description', 'text');
    }

    public function getName()
    {
        return 'course_material';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('storage'));
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
