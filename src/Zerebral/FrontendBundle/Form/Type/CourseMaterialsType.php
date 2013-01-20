<?php

namespace Zerebral\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Zerebral\CommonBundle\Form\Type\OptionalModelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Constraint;

use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Material\CourseMaterial;

class CourseMaterialsType extends AbstractType
{
    /**
     * Materials course
     * @var Course
     */
    protected $course;

    /**
     * Set course
     *
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Get course
     *
     * @return \Zerebral\BusinessBundle\Model\Course\Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $course = $this->getCourse();

        $builder->add('materials', 'collection', array(
            'type'   => new CourseMaterialType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'options'  => array(
                'required'  => false,
                'error_mapping' => array(
                    '.' => 'description',
                )
            ),
            'cascade_validation' => true,
            'constraints' => array(
                new \Symfony\Component\Validator\Constraints\All(array(
                    'constraints' => array(
                        new \Symfony\Component\Validator\Constraints\NotBlank(array('message' => 'Please, select file for uploading')),
                    )
                )),
            ),
        ));

        $builder->add('folder', new OptionalModelType(), array(
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

        $builder->addEventListener(\Symfony\Component\Form\FormEvents::POST_BIND, function(\Symfony\Component\Form\FormEvent $event) use ($course) {
            /** @var $materials CourseMaterial[] */
            $materials = $event->getForm()->get('materials')->getData();
            foreach($materials as $material) {
                if (!empty($material)) {
                    $material->setCourse($course);
                }

            }
        });

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
