<?php

namespace Zerebral\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Zerebral\BusinessBundle\Model\User as User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Zerebral\BusinessBundle\Model\User\GuardianInviteQuery;

class SecuredController extends Controller
{
    /**
     * @Route("/signin", name="_login")
     * @Template()
     */
    public function loginAction()
    {


        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'target' => 'login',
            'error' => $error,
            'passwordRestored' => $request->get('passwordRestored')
        );
    }


    /**
     *
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction()
    {
        return $this->redirect($this->generateUrl('_login'));
    }

    /**
     * @Route("/signup", name="signup")
     * @Template
     */
    public function signupAction()
    {
        $form = $this->createForm(new \Zerebral\FrontendBundle\Form\Type\UserSignupType());
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /** @var $user User\User */
                $user = $form->getData();
                $user->setPasswordEncoder($this->getPasswordEncoder($user));
                $user->transitToRoleModel()->save();

                //automatic log in user.
                $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
                $this->get('security.context')->setToken($token);

                return $this->get('zerebral.frontend.login_success_handler')->onAuthenticationSuccess($this->getRequest(), $token);
            }
        }


        $mainParameters = array(
            'form' => $form->createView(),
            'guardianMode' => false
        );
        $additionalParameters = array();



        $guardianInviteCode = $this->get('session')->get('guardian_invite_code', 0);
        $guardianInvite = GuardianInviteQuery::create()->filterByCode($guardianInviteCode)->filterByActivated(false)->findOne();
        if (!is_null($guardianInvite)) {
            $additionalParameters = array(
                'guardianMode' => true,
                'guardianInvite' => $guardianInvite
            );
        }

        return array_merge($mainParameters, $additionalParameters);
    }

    /**
     * Get password encoder for user
     *
     * @param \Zerebral\BusinessBundle\Model\User\User $user
     * @return PasswordEncoderInterface
     */
    protected function getPasswordEncoder($user)
    {
        $factory = $this->get('security.encoder_factory');
        return $factory->getEncoder($user);
    }

    /**
     * @Route("/forgot-password", name="_forgot_password")
     * @Template
     */
    public function forgotPasswordAction()
    {
        $data = array();
        if ($this->getRequest()->isMethod('POST')) {
            $email = $this->getRequest()->get('_username', null);
            if (is_null($email)) {
                $data['error'] = 'Please enter your email';
            } else {
                $user = \Zerebral\BusinessBundle\Model\User\UserQuery::create()->findOneByEmail($email);
                if (is_null($user)) {
                    $data['error'] = 'User with email "' . $email . '" is not registered';
                } else {
                    $code = md5($user->getId() . '_' . $user->getEmail() . '_' . time() . '_' . uniqid('password_reset'));
                    $user->setResetCode($code);
                    $user->save();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Zerebral - Password recovery')
                        ->setFrom('hello@zerebral.com')
                        ->setTo($email)
                        ->setBody(
                        $this->renderView(
                            'ZerebralFrontendBundle:Email:passwordRecovery.html.twig',
                            array(
                                'email' => $email,
                                'code' => $code,
                                'userName' => $user->getFullName(),
                                'host' => $this->getRequest()->getHttpHost()
                            )
                        )
                    );
                    $this->get('mailer')->send($message);
                    $this->getRequest()->getSession()->setFlash('reset_list_sent', 'Email with activation link was send to "' . $email . '"');
                    return $this->redirect($this->generateUrl('_login'));
                }
            }
        } else {
            $data['error'] = false;
        }
        return $data;
    }


    /**
     * @Route("/reset-password/{code}", name="_reset_password")
     * @Template
     */
    public function resetPasswordAction($code)
    {

        if (!is_null($this->getUser())) {
            return $this->redirect($this->generateUrl('dashboard'));
        }

        $user = \Zerebral\BusinessBundle\Model\User\UserQuery::create()->findOneByResetCode($code);
        if (is_null($user)) {
            return $this->redirect($this->generateUrl('_login'));
        }

        $form = $this->createForm(new \Zerebral\FrontendBundle\Form\Type\UserResetPasswordType(), $user);
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /** @var $user User\User */
                $user = $form->getData();

                $user->setPasswordEncoder($this->getPasswordEncoder($user));
                $user->setResetCode(null);
                $user->transitToRoleModel()->save();

                //automatic log in user.
                $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
                $this->get('security.context')->setToken($token);

                return $this->get('zerebral.frontend.login_success_handler')->onAuthenticationSuccess($this->getRequest(), $token);
            }
        }

        return array(
            'user' => $user,
            'form' => $form->createView(),
            'code' => $code
        );
    }
}
