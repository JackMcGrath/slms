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

class SecuredController extends Controller
{
    /**
     * @Route("/login", name="_login")
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
        // The security layer will intercept this request
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
                #TODO set correct role as 4th argument
                $token = new UsernamePasswordToken($user, null, 'secured_area');
                $this->get('security.context')->setToken($token);

                return $this->redirect($this->generateUrl('dashboard'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
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
}
