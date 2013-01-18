<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\FrontendBundle\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

// TODO: comments + type hints
class AssignmentSolutionType extends AbstractType
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
        $builder->add('files', 'collection', array(
            'type' => new \Zerebral\FrontendBundle\Form\Type\FileType(),
            'allow_add' => true,
            'allow_delete' => false,
            'by_reference' => false,
            'options' => array('storage' => $this->getFileStorage(), 'error_bubbling' => true, 'error_mapping' => array('name' => 'description'))
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