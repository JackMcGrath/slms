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

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

use \Criteria;

use \Zerebral\BusinessBundle\Model\Message\MessageQuery;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/messages")
 */
class MessageController extends \Zerebral\CommonBundle\Component\Controller
{
    public $messagesOnPage = 10;
    /**
     * @Route("/", name="messages_inbox")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function indexAction()
    {
        $page = $this->getRequest()->get('page', 1);

        $messages = MessageQuery::create()->filterInboxByUser($this->getUser())->paginate($page, $this->messagesOnPage)->getResults();
        $unreadCount = MessageQuery::create()->getUnreadCount($this->getUser());


        $paginator = new \Zerebral\FrontendBundle\Extension\Paginator(MessageQuery::create()->filterInboxByUser($this->getUser())->count(), $page , $this->messagesOnPage, 3);

        return array(
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'target' => 'messages',
            'folder' => 'inbox',
            'paginator' => $paginator,
        );
    }

    /**
     * @Route("/sent", name="messages_sent")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template("ZerebralFrontendBundle:Message:index.html.twig")
     */
    public function sentAction()
    {
        $page = $this->getRequest()->get('page', 1);

        $messages = MessageQuery::create()->filterSentByUser($this->getUser())->paginate($page, $this->messagesOnPage)->getResults();
        $unreadCount = MessageQuery::create()->getUnreadCount($this->getUser());

        $paginator = new \Zerebral\FrontendBundle\Extension\Paginator(MessageQuery::create()->filterSentByUser($this->getUser())->count(), $page , $this->messagesOnPage, 3);

        return array(
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'target' => 'messages',
            'folder' => 'sent',
            'paginator' => $paginator,
        );
    }

    /**
     * @Route("/reply/{threadId}", name="message_reply")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function replyAction($threadId)
    {
        $thread = MessageQuery::create()->findThreadForUser($threadId, $this->getUser());

        if (count($thread) == 0) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('You have not permissions to read this message.');
        }

        $newMessage = new Model\Message\Message();
        $newMessageType = new FormType\MessageType();

        $form = $this->createForm($newMessageType, $newMessage, array('validation_groups' => array('Default', 'reply')));
        if ($this->getRequest()->isMethod('POST')) {

            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $firstMessage = $thread->getFirst();

                $receiverId = $firstMessage->getToId() == $this->getUser()->getId() ? $firstMessage->getFromId() : $firstMessage->getToId();

                $newMessage = $form->getData();
                $newMessage->setFromId($this->getUser()->getId());
                $newMessage->setToId($receiverId);
                $newMessage->setUserId($receiverId);
                $newMessage->setThreadId($firstMessage->getThreadId());
                $newMessage->setSubject($firstMessage->getSubject());

                $newMessage->save();

                return $this->redirect($this->generateUrl('messages_inbox'));
            }
        }

        $readInThreadCount = 0;
        foreach ($thread as $message) {
            if ($message->getIsRead()) {
                $readInThreadCount++;
            } else {
                $message->markAsRead();
//                $readInThreadCount++;
            }
        }

        if ($readInThreadCount >= 5 && count($thread) > 5) {
            $collapsedMessagesCount = $readInThreadCount == count($thread) ? count($thread) - 1 : $readInThreadCount;
        } else {
            $collapsedMessagesCount = 0;
        }

        $unreadCount = MessageQuery::create()->getUnreadCount($this->getUser());

        return array(
            'thread' => $thread,
            'unreadCount' => $unreadCount,
            'collapsedMessagesCount' => $collapsedMessagesCount,
            'target' => 'messages',
            'folder' => 'inbox',
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/compose", name="message_compose")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function composeAction()
    {
        $newMessage = new Model\Message\Message();
        $newMessageType = new FormType\MessageType();

        $form = $this->createForm($newMessageType, $newMessage, array('validation_groups' => array('Default', 'compose')));
        if ($this->getRequest()->isMethod('POST')) {

            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $newMessage = $form->getData();
                $newMessage->setFromId($this->getUser()->getId());
                $newMessage->setUserId($newMessage->getToId());

                $newMessage->save();

                return $this->redirect($this->generateUrl('messages_inbox'));
            }
        }

        $unreadCount = MessageQuery::create()->getUnreadCount($this->getUser());

        return array(
            'target' => 'messages',
            'folder' => 'inbox',
            'unreadCount' => $unreadCount,
            'hasErrors' => $this->getRequest()->isMethod('POST'),
            'form' => $form->createView(),
        );
    }

     /**
     * @Route("/edit", name="message_edit")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function editAction()
    {
        /** @var \Zerebral\BusinessBundle\Model\User\User $user */
        $user = $this->getUser();
        $threads = $this->getRequest()->get('Collection', array());
        if ($threads) {
            $messages = \Zerebral\BusinessBundle\Model\Message\MessageQuery::create()->filterByUserId($this->getUser()->getId())->filterByThreadId($threads)->find();
            if ($this->getRequest()->get('delete', false)) {
                /** @var \Zerebral\BusinessBundle\Model\Message\Message $message */
                foreach ($messages as $message) {
                    if ($message->getUserId() != $user->getId()) {
                        throw new AccessDeniedHttpException('You have not permissions to delete this message.');
                    }
                    $message->delete();
                    return $this->redirect($this->getRequest()->headers->get('referer'));

                }
            } else if ($this->getRequest()->get('mark-as-read', false)) {
                foreach ($messages as $message) {
                    if ($message->getUserId() != $user->getId()) {
                        throw new AccessDeniedHttpException('You have not permissions to edit this message.');
                    }
                    $message->setIsRead(true);
                    $message->save();
                }
            } else if ($this->getRequest()->get('mark-as-unread', false)) {
                foreach ($messages as $message) {
                    if ($message->getUserId() != $user->getId()) {
                        throw new AccessDeniedHttpException('You have not permissions to edit this message.');
                    }
                    $message->setIsRead(false);
                    $message->save();
                }
            }
        }

        return $this->redirect($this->generateUrl('messages_inbox'));
    }
}
