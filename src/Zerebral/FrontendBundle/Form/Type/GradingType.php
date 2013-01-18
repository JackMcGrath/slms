<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GradingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('grading', 'text', array('required' => false, 'max_length' => 10));
        $builder->add('grading_comment', 'textarea', array('required' => false));
    }

    public function getName()
    {
        return 'grading';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zerebral\BusinessBundle\Model\Assignment\StudentAssignment',
            'validation_groups' => array('grading')
        ));
    }
}
