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

use Zerebral\BusinessBundle\Calendar\EventProviders\AssignmentEventsProvider;
use Zerebral\BusinessBundle\Calendar\EventProviders\CourseAssignmentEventsProvider;

use Zerebral\CommonBundle\Component\Calendar\Calendar;

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
        $provider = new CourseAssignmentEventsProvider($this->getRoleUser()->getAssignments());
        $currentMonth = new Calendar(time(), $provider);
        $nextMonth = new Calendar(strtotime("+1 month"), $provider);

        $upcomingAssignments = $this->getRoleUser()->getUpcomingAssignments();

        return array(
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
            'upcomingAssignments' => $upcomingAssignments,
            'courses' => $this->getRoleUser()->getCourses(),
            'target' => 'courses',
            'courseJoinForm' => $this->createForm(new FormType\CourseJoinType())->createView()
        );
    }

    /**
     * @Route("/view/{id}", name="course_view")
     * @ParamConverter("course")
     * @SecureParam(name="course", permissions="VIEW")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function viewAction(Model\Course\Course $course)
    {
        $upcomingAssignments = $this->getRoleUser()->getUpcomingAssignments($course);

        return array(
            'course' => $course,
            'upcomingAssignments' => $upcomingAssignments,
            'target' => 'courses',
            'showWelcomeMessage' => $this->getRequest()->get('showWelcomeMessage', false)
        );
    }

    /**
     * @Route("/add", name="course_add")
     * @Route("/edit/{id}", name="course_edit")
     * @ParamConverter("course")
     *
     * @SecureParam(name="course", permissions="EDIT")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function addAction(Model\Course\Course $course = null)
    {
        $courseType = new FormType\CourseType();
        $courseType->setTeacher($this->getRoleUser());
        $form = $this->createForm($courseType, $course);

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /** @var $course Model\Course\Course */
                $course = $form->getData();
                $course->setCreatedBy($this->getRoleUser()->getId());
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
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function deleteAction(Model\Course\Course $course)
    {
        $course->delete();
        $this->setFlash('delete_course_success', 'Course <b>' . $course->getName() . '</b> has been successfully deleted.');
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
        $assignments = $this->getRoleUser()->getCourseAssignments($course);
        $provider = new AssignmentEventsProvider($assignments);
        $currentMonth = new \Zerebral\CommonBundle\Component\Calendar\Calendar(time(), $provider);
        $nextMonth = new \Zerebral\CommonBundle\Component\Calendar\Calendar(strtotime("+1 month"), $provider);

        return array(
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
            'assignments' => $assignments,
            'course' => $course,
            'target' => 'courses'
        );
    }

    /**
     * @Route("/syllabus/{id}", name="course_materials")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course")
     * @Template()
     */
    public function materialsAction(Model\Course\Course $course)
    {
        $dayMaterials = array();

        $dayMaterials[strtotime('now')] = array('1', '2');
        $dayMaterials[strtotime('-2 days')] = array('1', '2');

        return array(
            'dayMaterials' => $dayMaterials,
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
            'courseInviteForm' => $this->createForm(new FormType\CourseInviteType())->createView(),
            'target' => 'members'
        );
    }

    /**
     * @Route("/remove-student/{courseId}/{studentId}", name="course_remove_student")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @ParamConverter("course", options={"mapping": {"courseId": "course_id"}})
     * @ParamConverter("student", options={"mapping": {"studentId": "student_id"}})
     */
    public function removeStudent(Model\Course\CourseStudent $courseStudent)
    {
        $courseStudent->delete();
        $this->setFlash('delete_course_student_success', 'Student <b>' . $courseStudent->getStudent()->getFullName() . '</b> has been successfully deleted from course.');
        return $this->redirect($this->generateUrl('course_members', array('id' => $courseStudent->getCourseId())));
    }

    /**
     * @Route("/reset/{id}", name="course_reset_code")
     * @ParamConverter("course")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function resetAccessCodeAction(Model\Course\Course $course = null)
    {
        $course->resetAccessCode();
        $course->save();

        return $this->redirect(
            $this->generateUrl(
                'course_members',
                array(
                    'id' => $course->getId()
                )
            )
        );
    }
}
