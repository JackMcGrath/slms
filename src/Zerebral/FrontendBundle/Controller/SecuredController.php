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
        $request = $this->getRequest();

        $user = new User\User();
        if ($request->isMethod('post')) {
            $user = $this->getModelByRole($request->get('role', User\User::ROLE_TEACHER));

            try {
                $user->setPasswordEncoder($this->getPasswordEncoder($user));
                $user->setEmail($request->get('email'));
                $user->setPlainPassword($request->get('password'));
                $user->setPasswordConfirmation($request->get('password_confirmation'));
                $user->setFirstName($request->get('first_name'));
                $user->setLastName($request->get('last_name'));
                $user->setRole($request->get('role'));

                if ($user->validate()) {
                    $user->save();
                    return $this->redirect($this->generateUrl('_login'));
                }

            } catch (\Exception $e) {
//               $e->getMessage();
            }
        }

        return array(
            'user' => $user
        );
    }

    /**
     * @param $role
     * @return \Zerebral\BusinessBundle\Model\User\User
     * @throws \Exception
     */
    protected function getModelByRole($role)
    {
        switch ($role) {
            case User\User::ROLE_STUDENT :
                return new User\Student();
                break;
            case User\User::ROLE_TEACHER :
                return new User\Teacher();
                break;
            default:
                throw new \Exception('role');
        }
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
