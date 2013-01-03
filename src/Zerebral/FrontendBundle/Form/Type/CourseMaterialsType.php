<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Constraint;

class CourseMaterialsType extends AbstractType
{
    protected $fileStorage;
    protected $course;


    public function setFileStorage($fileStorage)
    {
        $this->fileStorage = $fileStorage;
    }

    public function getFileStorage()
    {
        return $this->fileStorage;
    }

    public function setCourse($course)
    {
        $this->course = $course;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $course = $this->getCourse();

        $builder->add('courseMaterials', 'collection', array(
            'type'   => new CourseMaterialType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'options'  => array(
                'required'  => false,
                'storage' => $this->getFileStorage()
            ),
            'cascade_validation' => true,
        ));

        $builder->add('courseMaterialFolder', new OptionalModelType(), array(
            'choices' => \Zerebral\BusinessBundle\Model\Material\CourseFolderQuery::create()->findAvailableByCourse($course),
            'create_model' => function($text) use ($course) {
                $courseFolder = new \Zerebral\BusinessBundle\Model\Material\CourseFolder();
                $courseFolder->setName($text);
                $courseFolder->setCourseId($course->getId());
                return $courseFolder;
            },
            'required' => false,
            'empty_value' => "No folder",
            'create_new_label' => 'Create new folder',
            'choose_exists_label' => 'Choose existing folder',
            'cascade_validation' => true,
        ));

    }

    public function getName()
    {
        return 'course_materials';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'cascade_validation' => true,
                'csrf_protection' => false,
            )
        );
    }
}
