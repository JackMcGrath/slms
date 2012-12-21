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
use Zerebral\BusinessBundle\Model\Course\CourseStudentQuery;

use Zerebral\BusinessBundle\Calendar\EventProviders\AssignmentEventsProvider;
use Zerebral\BusinessBundle\Calendar\EventProviders\CourseAssignmentEventsProvider;

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
        $currentMonth = new \Zerebral\CommonBundle\Component\Calendar\Calendar(time(), $provider);
        $nextMonth = new \Zerebral\CommonBundle\Component\Calendar\Calendar(strtotime("+1 month"), $provider);

        $form = $this->createForm(new FormType\AccessCodeType(), new Model\Course\AccessCode());

        if($this->getRequest()->isMethod('POST')){
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /** @var $invite Model\Course\AccessCode */
                $invite = $form->getData();

                return $this->redirect($this->generateUrl('course_accept_invite', array(
                    'accessCode' => $invite->getAccessCode(),
                )));
            }
        }

        return array(
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
            'courses' => $this->getRoleUser()->getCourses(),
            'target' => 'courses',
            'form' => $form->createView()
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
                        ->setSubject('Course invitation')
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
     * @Route("/accept/{accessCode}", name="course_accept_invite")
     * @Route("/accept", name="course_accept")
     * @ParamConverter("course", options={"mapping": {"accessCode": "access_code"}})
     */
    public function acceptInviteAction(Model\Course\Course $course = null){
        $user = $this->getRoleUser();

        if(!$course){
            throw $this->createNotFoundException('The course not found');
        }

        if(empty($user)){
            $this->getRequest()->getSession()->set('access_code', $course->getAccessCode());
            return $this->redirect($this->generateUrl('signup', array()));
        }else{
            if($user->hasCourse($course)){
                throw $this->createNotFoundException('User already assigned to course');
            }

            if($user instanceof \Zerebral\BusinessBundle\Model\User\Student){
                $course->addStudent($this->getRoleUser());
            }else{
                $course->addTeacher($this->getRoleUser());
            }
            $course->save();
            return $this->redirect($this->generateUrl('course_view', array(
                        'id' => $course->getId(),
                        'showWelcomeMessage' => true
                    )
            ));
        }
    }

    /**
     * @Route("/reset/{id}", name="course_reset_code")
     * @ParamConverter("course")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function resetAccessCodeAction(Model\Course\Course $course = null){
        $course->resetAccessCode();
        $course->save();
        return $this->redirect($this->generateUrl('course_members', array(
                    'id' => $course->getId()
        )));
    }
}
