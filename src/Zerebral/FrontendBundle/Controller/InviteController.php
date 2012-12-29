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
}
