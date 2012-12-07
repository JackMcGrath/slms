<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/courses")
 */
class CourseController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="courses")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'courses' => Model\Course\CourseQuery::create()->find(),
            'target' => 'courses'
        );
    }

    /**
     * @Route("/view/{id}", name="course_view")
     * @ParamConverter("course")
     * @Template()
     */
    public function viewAction(Model\Course\Course $course)
    {
        return array(
            'course' => $course,
            'target' => 'courses'
        );
    }

    /**
     * @Route("/add", name="course_add")
     * @Route("/edit/{id}", name="course_edit")
     * @ParamConverter("course")
     * @Template()
     */
    public function addAction(Model\Course\Course $course = null)
    {
        $form = $this->createForm(new FormType\CourseType(), $course);
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /** @var $course Model\Course\Course */
                $course = $form->getData();
                $course->setCreatedBy($this->getUser()->getTeacher()->getId());
                $course->save();

                return $this->redirect($this->generateUrl('course_view', array('id' => $course->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
            'target' => 'courses'
        );
    }

    /**
     * @Route("/delete/{id}", name="course_delete")
     * @ParamConverter("course")
     * @Template()
     */
    public function deleteAction(Model\Course\Course $course)
    {
        $course->delete();
        return $this->redirect($this->generateUrl('courses'));
    }
}
