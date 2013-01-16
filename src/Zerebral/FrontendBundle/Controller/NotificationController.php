<?php
namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Symfony\Component\HttpFoundation\JsonResponse;
use Zerebral\CommonBundle\HttpFoundation\FormJsonResponse;

/**
 * @Route("/notifications")
 */
class NotificationController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="notifications")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function indexAction()
    {
        /** @var $user \Zerebral\BusinessBundle\Model\User\User */
        $user = $this->getUser();

        $notifications = \Zerebral\BusinessBundle\Model\Notification\NotificationQuery::create()->findUnreadByUserId($user->getId());

        return array(
            'notifications' => $notifications->find(),
            'target' => 'notifications'
        );
    }

    /**
     * @Route("/unread-list", name="ajax_unread_list")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     */
    public function unreadListAction()
    {
        /** @var $user \Zerebral\BusinessBundle\Model\User\User */
        $user = $this->getUser();

        $notifications = \Zerebral\BusinessBundle\Model\Notification\NotificationQuery::create()->findUnreadByUserId($user->getId());
        $content = $this->render('ZerebralFrontendBundle:Notification:list.html.twig', array('notifications' => $notifications->find()))->getContent();

        //$notifications->update(array('IsRead' => true));
        return new JsonResponse(array('has_errors' => false, 'content' => $content));
    }
}
