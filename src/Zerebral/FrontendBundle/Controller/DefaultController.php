<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends \Zerebral\CommonBundle\Components\Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {

        return array('name' => 'dev');
    }
}
