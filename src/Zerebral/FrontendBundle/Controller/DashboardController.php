<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Zerebral\FrontendBundle\Form\Type as FormType;

class DashboardController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="dashboard")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function indexAction()
    {
//        foreach(\Zerebral\BusinessBundle\Model\Notification\NotificationQuery::create()->find() as $not) {
//            echo $not->getId() . ' ' . get_class($not) . PHP_EOL;
//        }
//        die();

        $feedItemFormType = new FormType\FeedItemType();
        $feedItemForm = $this->createForm($feedItemFormType, null);

        $feedItemQuery = new \Zerebral\BusinessBundle\Model\Feed\FeedItemQuery();
        $feeds = $feedItemQuery->getItemsForUser($this->getUser());

        return array(
            'target' => 'home',
            'feedItemForm' => $feedItemForm->createView(),
            'feedItems' => $feeds,
            'target' => 'feed',
        );
    }
}
