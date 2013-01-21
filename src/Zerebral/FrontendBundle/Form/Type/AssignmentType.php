<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Zerebral\CommonBundle\Form\DataTransformer\TimeTransformer;
use Zerebral\CommonBundle\File\Form\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategory;

class AssignmentType extends AbstractType
{

    /**
     * Required for new assignment category
     *
     * @var \Zerebral\BusinessBundle\Model\User\Teacher
     */
    protected $teacher;

    /**
     * Set teacher
     * Will be used in new assignment category
     *
     * @param \Zerebral\BusinessBundle\Model\User\Teacher $teacher
     */
    public function setTeacher(\Zerebral\BusinessBundle\Model\User\Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false, 'max_length' => 200));
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
                'choose_exists_label' => 'Choose existing category',
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
                'type' => new FileType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'options' => array(
                    'error_bubbling' => true,
                    'folder' => 'assignment',
                )
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