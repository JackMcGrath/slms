<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Zerebral\CommonBundle\Form\DataTransformer\TimeTransformer;
use Zerebral\CommonBundle\Form\DataTransformer\DefaultValueTransformer;
use Zerebral\FrontendBundle\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

class AssignmentType extends AbstractType
{

    protected $teacher;
    protected $fileStorage;


    public function setFileStorage($fileStorage) {
        $this->fileStorage = $fileStorage;
    }

    public function getFileStorage() {
        return $this->fileStorage;
    }

    /**
     * Set teacher model to class instance for using it in callback function
     * @param \Zerebral\BusinessBundle\Model\User\Teacher $teacher
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false));
        $builder->add('description', 'textarea', array('required' => false));

        $builder->add('assignmentCategory', 'model', array(
            'class' => 'Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory',
            'property' => 'name',
            'required' => false,
            'empty_value' => "What's assignment category?",
            'empty_data' => 0,
            'invalid_message' => 'Category is required',
        ));

        $builder->add('maxPoints', 'text', array('required' => false));

        $builder->add('dueAt', 'datetime', array(
            'required' => false,
            'date_widget' => 'single_text',
            'date_format' => 'MM/dd/yyyy',
            'time_widget' => 'single_text'
        ));

        $builder->add('files',  'collection', array(
            'type' => new \Zerebral\FrontendBundle\Form\Type\FileType(),
            'allow_add' => true,
            'by_reference' => false,
            'options' => array('storage' => $this->getFileStorage())
        ));

        $builder->get('dueAt')->get('time')->addViewTransformer(new TimeTransformer());
        $builder->get('maxPoints')->addViewTransformer(new DefaultValueTransformer('100'));

    }

    public function getName()
    {
        return 'assignment';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Zerebral\BusinessBundle\Model\Assignment\Assignment',
            )
        );
    }
}