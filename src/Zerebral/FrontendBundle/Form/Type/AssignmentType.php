<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Zerebral\CommonBundle\Form\DataTransformer\TimeTransformer;
use Zerebral\FrontendBundle\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Collection;

use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory;

class AssignmentType extends AbstractType
{

    protected $teacher;
    protected $fileStorage;


    public function setFileStorage($fileStorage)
    {
        $this->fileStorage = $fileStorage;
    }

    public function getFileStorage()
    {
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

        $teacher = $this->teacher;
        $builder->add(
            'assignmentCategory',
            new OptionalModelType(),
            array(
                'choices' => AssignmentCategoryQuery::create()->findAvailableByTeacher($teacher),
                'create_model' => function($text) use ($teacher) {
                    $assignmentCategory = new AssignmentCategory();
                    $assignmentCategory->setName($text);
                    $assignmentCategory->setTeacher($teacher);
                    return $assignmentCategory;
                },
                'required' => false,
                'empty_value' => "What's assignment category?",
                'invalid_message' => 'Category is required',

                'create_new_label' => 'Create new category',
                'choose_exists_label' => 'Choose exists category',
                'placeholder' => 'Category name',
            )
        );

        $builder->add('maxPoints', 'text', array('required' => false));

        $builder->add(
            'dueAt',
            'datetime',
            array(
                'required' => false,
                'date_widget' => 'single_text',
                'date_format' => 'MM/dd/yyyy',
                'time_widget' => 'single_text'
            )
        );

        $builder->add(
            'files',
            'collection',
            array(
                'type' => new \Zerebral\FrontendBundle\Form\Type\FileType(),
                'allow_add' => true,
                'allow_delete' => false,
                'by_reference' => false,
                'options' => array('storage' => $this->getFileStorage(), 'error_bubbling' => true)
            )
        );

        $builder->get('dueAt')->get('time')->addViewTransformer(new TimeTransformer());
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