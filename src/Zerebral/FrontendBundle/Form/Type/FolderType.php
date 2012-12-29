<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FolderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => true, 'max_length' => 255));
        $builder->add('id', 'hidden', array('required' => false));
        $builder->add('course_id', 'hidden', array('required' => true));
    }

    public function getName()
    {
        return 'folder';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zerebral\BusinessBundle\Model\Material\CourseFolder',
            'error_mapping' => array('.' => 'name')
        ));
    }
}
