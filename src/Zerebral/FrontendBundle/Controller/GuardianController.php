<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

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
        return array();
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
    public function assignementsAction()
    {
        return array();
    }
}
