<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/courses")
 */
class CourseController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="courses")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'courses' => $this->getRoleUser()->getCourses(),
            'target' => 'courses'
        );
    }

    /**
     * @Route("/view/{id}", name="course_view")
     * @ParamConverter("course")
     *
     * @SecureParam(name="course", permissions="VIEW")
     *
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
     *
     * @SecureParam(name="course", permissions="EDIT")
     *
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
                $course->setCreatedBy($this->getRoleUser()->getId());
                $course->clearTeachers();
                $course->addTeacher($this->getRoleUser());
                $course->save();

                return $this->redirect($this->generateUrl('course_view', array('id' => $course->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
            'isFirstCourse' => $this->getRoleUser()->countCourses() == 0,
            'target' => 'courses'
        );
    }

    /**
     * @Route("/delete/{id}", name="course_delete")
     * @ParamConverter("course")
     *
     * @SecureParam(name="course", permissions="DELETE")
     *
     * @Template()
     */
    public function deleteAction(Model\Course\Course $course)
    {
        $course->delete();
        return $this->redirect($this->generateUrl('courses'));
    }


    /**
     * @Route("/assignments/{id}", name="course_assignments")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course")
     * @Template()
     */
    public function assignmentsAction(Model\Course\Course $course)
    {
        return array(
            'assignments' => $course->getAssignments(),
            'course' => $course,
            'target' => 'courses'
        );
    }

    /**
     * @Route("/members/{id}", name="course_members")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course")
     * @Template()
     */
    public function membersAction(Model\Course\Course $course)
    {
        return array(
            'students' => $course->getStudents(),
            'teachers' => $course->getTeachers(),
            'course' => $course,
            'target' => 'members'
        );
    }
}
