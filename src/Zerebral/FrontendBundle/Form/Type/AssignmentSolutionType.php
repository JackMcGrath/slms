<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\File\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

class AssignmentSolutionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('files', 'collection', array(
            'type' => new FileType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'options' => array(
                'error_bubbling' => true,
                'error_mapping' => array(
                    'name' => 'description'
                ),
                'has_description' => true,
                'folder' => 'assignment-solution'
            )
        ));
    }

    public function getName()
    {
        return 'assignment_solution';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Assignment\StudentAssignment',
            )
        );
    }
}