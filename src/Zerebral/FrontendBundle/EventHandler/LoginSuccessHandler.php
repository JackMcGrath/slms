<?php

namespace Zerebral\FrontendBundle\EventHandler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    protected $router;
    protected $security;

    public function __construct(Router $router, SecurityContext $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $session = $request->getSession();
        if($session->get('access_code', false)){
            $code = $session->get('access_code');
            $session->set('access_code', null);
            return new RedirectResponse($this->router->generate('course_accept_invite', array(
                        'accessCode' => $code
                    ))
            );
        }

        if ($this->security->isGranted('ROLE_TEACHER') && $this->security->getToken()->getUser()->getRoleModel()->countCourses() == 0) {
            return new RedirectResponse($this->router->generate('course_add'));
        }

        return new RedirectResponse($this->router->generate('courses'));
    }

}