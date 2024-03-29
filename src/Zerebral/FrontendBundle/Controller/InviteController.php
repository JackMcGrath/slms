<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Zerebral\CommonBundle\HttpFoundation\FormJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;
use Zerebral\BusinessBundle\Model\Course\CourseQuery;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\User\GuardianInviteQuery;
use Zerebral\BusinessBundle\Model\User\GuardianInvite;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;


class InviteController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/course/join/{accessCode}", name="course_join")
     * @Template()
     */
    public function joinAction()
    {
        $user = $this->getRoleUser();

        $course = new Course();
        $course->setAccessCode($this->getRequest()->get('accessCode'));

        $errors = $this->get('validator')->validate($course, array('accept_invite'));

        if (count($errors) == 0) {

            if (empty($user)) {
                $this->getRequest()->getSession()->set('access_code', $course->getAccessCode());
                return $this->redirect($this->generateUrl('signup', array()));
            }

            /** @var $course Course */
            $course = CourseQuery::create()->findOneByAccessCode($course->getAccessCode());
            $joined = $course->addUser($this->getRoleUser());


            if ($joined) {
                $course->save();
            }

            return $this->redirect(
                $this->generateUrl(
                    'course_view',
                    array(
                        'id' => $course->getId(),
                        'showWelcomeMessage' => $joined
                    )
                )
            );
        }

        throw $this->createNotFoundException($errors[0]->getMessage());
    }

    /**
     * @Route("/course/join", name="ajax_course_join")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     */
    public function ajaxJoinAction()
    {
        $form = $this->createForm(new FormType\CourseJoinType());

        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $form->bind($this->getRequest());
        if ($form->isValid()) {
            /** @var $course Course */
            $course = CourseQuery::create()->findOneByAccessCode($form['accessCode']->getData());
            $joined = $course->addUser($this->getRoleUser());
            if ($joined) {
                $course->save();
                if ($this->getUser()->isStudent()) {
                    /** @var $courseStudent Model\Course\CourseStudent */
                    $courseStudent = \Zerebral\BusinessBundle\Model\Course\CourseStudentQuery::create()->filterByCourse($course)->filterByStudent($this->getRoleUser())->findOne();
                    $courseStudent->setIsActive(false);
                    $courseStudent->save();
                }
            }

            return new JsonResponse(array(
                'redirect' => $this->generateUrl(
                    'course_view',
                    array(
                        'id' => $course->getId(),
                        'showWelcomeMessage' => $joined
                    )
                )
            ));
        }

        return new FormJsonResponse($form);
    }

    /**
     * @Route("/members/{id}", name="ajax_course_send_invites")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course")
     */
    public function sendAction(Model\Course\Course $course)
    {
        $form = $this->createForm(new FormType\CourseInviteType());

        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $emails = $form['emails']->getData();

            foreach ($emails as $email) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Zerebral - Invitation Notice')
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

            if (count($emails)) {
                $this->get('event_dispatcher')->dispatch('courses.invite_student', new ModelEvent($course));

            }

            $this->setFlash('invites_send', 'Invitations have been sent');

            return new JsonResponse(array(
                'redirect' => $this->generateUrl(
                    'course_members',
                    array(
                        'id' => $course->getId()
                    )
                )
            ));
        }

        return new \Zerebral\CommonBundle\HttpFoundation\FormJsonResponse($form);

    }


    /**
     * @Route("/invite-guardians", name="ajax_guardians_send_invites")
     * @PreAuthorize("hasRole('ROLE_STUDENT')")
     */
    public function inviteGuardiansAction()
    {
        $form = $this->createForm(new FormType\CourseInviteType());

        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $emails = $form['emails']->getData();

            $student = $this->getRoleUser();

            foreach ($emails as $email) {
                $code = md5($student->getId() . '_' . $student->getUserId() . '_' . $email . '_' . time() . uniqid('invite'));
                $guardianInvite = new GuardianInvite();
                $guardianInvite->setGuardianEmail($email);
                $guardianInvite->setStudent($student);
                $guardianInvite->setCode($code);
                $guardianInvite->setActivated(false);
                $guardianInvite->save();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Zerebral - Invitation Notice')
                    ->setFrom('hello@zerebral.com')
                    ->setTo($email)
                    ->setBody(
                    $this->renderView(
                        'ZerebralFrontendBundle:Email:guardianInvite.html.twig',
                        array(
                            'code' => $code,
                            'student' => $student,
                            'host' => $this->getRequest()->getHttpHost()
                        )
                    )
                );
                $this->get('mailer')->send($message);
            }

            $this->setFlash('invites_send', 'Invitations have been sent');

            return new JsonResponse(array('redirect' => $this->generateUrl('myprofile')));
        }

        return new \Zerebral\CommonBundle\HttpFoundation\FormJsonResponse($form);
    }

    /**
     * @Route("/join/{code}", name="guardian_join")
     * @Template()
     */
    public function guardianJoinAction($code)
    {
        if ((!is_null($this->getRoleUser())) && (!($this->getRequest()->getSession()->has('guardian_invite_code')))) {
            return $this->redirect($this->generateUrl('dashboard'));
        }
        /** @var GuardianInvite $guardianInvite  */
        $guardianInvite = GuardianInviteQuery::create()->filterByCode($code)->filterByActivated(false)->findOne();

        if (!is_null($this->getUser())) {
            if (!is_null($guardianInvite)) {
                /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
                $guardian = $this->getRoleUser();
                $guardian->addStudent($guardianInvite->getStudent());
                $guardian->save();
                $guardianInvite->setActivated(true);
                $guardianInvite->save();
                $this->getRequest()->getSession()->set('guardian_invite_code', null);
                $this->setFlash('child_added', 'Student ' . $guardianInvite->getStudent()->getFullName() . ' was successfully added as your child');
            }
            return $this->redirect($this->generateUrl('dashboard'));
        }

        if (is_null($guardianInvite)) {
            throw $this->createNotFoundException('This link is wrong or expired');
        }

        $this->getRequest()->getSession()->set('guardian_invite_code', $guardianInvite->getCode());
        return $this->redirect($this->generateUrl('signup', array()));
    }

    /**
     * @Route("/confirm/{courseId}/{studentId}", name="confirm_student_course", defaults={"action" = "confirm"})
     * @Route("/decline/{courseId}/{studentId}", name="decline_student_course", defaults={"action" = "decline"})
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @ParamConverter("student", options={"mapping": {"studentId": "id"}})
     */
    public function confirmStudentAction(Model\Course\Course $course, Model\User\Student $student, $action = null)
    {
        /** @var $courseStudent Model\Course\CourseStudent */
        $courseStudent = \Zerebral\BusinessBundle\Model\Course\CourseStudentQuery::create()->filterByCourse($course)->filterByStudent($student)->findOne();

        if ($action == 'confirm') {
            $courseStudent->setIsActive(true);
        } else if ($action == 'decline') {
            $courseStudent->setIsActive(false);
        }
        $courseStudent->save();
        return $this->redirect($this->generateUrl('course_members', array('id' => $course->getId())));
    }
}
