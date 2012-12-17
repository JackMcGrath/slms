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
        $provider = new AssignmentEventsProvider($this->getRoleUser()->getAssignments());
        $currentMonth = new \Zerebral\CommonBundle\Component\Calendar\Calendar(time(), $provider);
        $nextMonth = new \Zerebral\CommonBundle\Component\Calendar\Calendar(strtotime("+1 month"), $provider);

        return array(
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
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
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
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
     * @Route("/members/{id}", name="course_members")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course")
     * @Template()
     */
    public function membersAction(Model\Course\Course $course)
    {
        $form = $this->createForm(new FormType\MembersType(), new Model\Course\Member());
        if ($this->getRequest()->isMethod("POST")) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /**
                 * @var \Zerebral\BusinessBundle\Model\Course\Member $memberInviteForm
                 */
                $memberInviteForm = $form->getData();
                $emails = $memberInviteForm->getEmailList();

                foreach ($emails as $email) {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Welcome to zerebral')
                        ->setFrom('hello@zerebral.com')
                        ->setTo($email)
                        ->setBody(
                        $this->renderView(
                            'ZerebralFrontendBundle:Email:invite.html.twig',
                            array(
                                'course' => $course,
                                'user' => $this->getRoleUser()->getUser(),
                                'host' => $this->getRequest()->getHttpHost()
                            )
                        )
                    );
                    $this->get('mailer')->send($message);
                }
            }
        }

        return array(
            'students' => $course->getStudents(),
            'teachers' => $course->getTeachers(),
            'course' => $course,
            'form' => $form->createView(),
            'target' => 'members'
        );
    }

    /**
     * @Route("/accept/{accessCode}", name="course_accept_invite")
     * @ParamConverter("course", options={"mapping": {"accessCode": "access_code"}})
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     *
     * @Template()
     */
    public function acceptInviteAction(Model\Course\Course $course){
        $user = $this->getRoleUser();

        if($user instanceof \Zerebral\BusinessBundle\Model\User\Student){
            $course->addStudent($this->getRoleUser());
        }else{
            $course->addTeacher($this->getRoleUser());
        }
        $course->save();
        return $this->redirect($this->generateUrl('course_view', array('id' => $course->getId())));
    }
}
