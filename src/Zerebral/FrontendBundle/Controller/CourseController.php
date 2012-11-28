<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/courses")
 */
class CourseController extends \Zerebral\CommonBundle\Components\Controller
{
    /**
     * @Route("/", name="courses")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'courses' => Model\Course\CourseQuery::create()->find()
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
            'course' => $course
        );
    }

    /**
     * @Route("/edit/{id}", name="course_edit")
     * @ParamConverter("course")
     * @Template()
     */
    public function editAction(Model\Course\Course $course)
    {
        throw new \Exception('Not implemented!');
        return array(
            'course' => $course
        );
    }

    /**
     * @Route("/add", name="course_add")
     * @Template()
     */
    public function addAction()
    {
        throw new \Exception('Not implemented!');
        return array(
            'course' => new Model\Course\Course()
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
