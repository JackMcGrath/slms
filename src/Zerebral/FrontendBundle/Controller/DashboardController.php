<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Zerebral\FrontendBundle\Form\Type as FormType;

use \Zerebral\BusinessBundle\Model\Feed\FeedItemQuery;

class DashboardController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="dashboard")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function indexAction()
    {
  //      var_dump($this->container->get('security.context'));
        if ($this->getUser()->isGuardian()) {
            return $this->redirect($this->generateUrl('guardian_summary'));
        }

        $feedItemFormType = new FormType\FeedItemType();
        $feedItemForm = $this->createForm($feedItemFormType, null);

        $feedItems = FeedItemQuery::create()->getGlobalFeed($this->getUser())->limit(10)->find();
        $feedItemsCount = FeedItemQuery::create()->getGlobalFeed($this->getUser())->count();


        return array(
            'target' => 'home',
            'feedItemForm' => $feedItemForm->createView(),
            'feedItems' => $feedItems,
            'feedItemsCount' => $feedItemsCount
        );
    }
}
