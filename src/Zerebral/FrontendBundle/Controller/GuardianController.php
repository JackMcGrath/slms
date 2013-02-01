<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Zerebral\BusinessBundle\Model\User\Student;

/**
  * @Route("/parent-area")
 */
class GuardianController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="guardian_summary")
     * @Secure(roles="ROLE_GUARDIAN")
     * @Template
     */
    public function indexAction()
    {
        return array(
            'target' => 'home'
        );
    }

    /**
     * @Route("/set-child/{childId}", name="guardian_set_child")
     * @Secure(roles="ROLE_GUARDIAN")
     * @param \Zerebral\BusinessBundle\Model\User\Student $student
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("student", options={"mapping": {"childId": "id"}})
     */
    public function setSelectedChild(\Zerebral\BusinessBundle\Model\User\Student $student)
    {
        $referrer = $this->getRequest()->headers->get('referer');
        if (($this->getRoleUser()->isGuardianFor($student)) && (!is_null($referrer))) {
            $this->get('session')->set('selectedChildId', $student->getId());
            return $this->redirect($this->getRequest()->headers->get('referer'));
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('You can\'t access this URL directly');
        }

    }

    /**
     * @Route("/attendace", name="guardian_attendance")
     * @Secure(roles="ROLE_GUARDIAN")
     * @Template
     */
    public function attendaceAction()
    {
        return array();
    }

    /**
     * @Route("/assignments", name="guardian_assignments")
     * @Secure(roles="ROLE_GUARDIAN")
     * @Template
     */
    public function assignmentsAction()
    {
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $child = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));
        return array(
            'target' => 'home',
            'guardian' => $guardian,
            'child' => $child
        );
    }
}
