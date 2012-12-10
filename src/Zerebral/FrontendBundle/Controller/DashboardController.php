<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DashboardController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="dashboard")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'name' => 'dev',
            'target' => 'home'
        );
    }
}
