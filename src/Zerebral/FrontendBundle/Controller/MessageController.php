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

/**
 * @Route("/messages")
 */
class MessageController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="messages_inbox")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function indexAction()
    {

        $messages = MessageQuery::create()->findInboxByUser($this->getUser());
        $unreadCount = MessageQuery::create()->getUnreadCount($this->getUser());

        return array(
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'target' => 'messages',
            'folder' => 'inbox',
        );
    }

    /**
     * @Route("/sent", name="messages_sent")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template("ZerebralFrontendBundle:Message:index.html.twig")
     */
    public function sentAction()
    {

        $messages = MessageQuery::create()->findSentByUser($this->getUser());
        $unreadCount = MessageQuery::create()->getUnreadCount($this->getUser());

        return array(
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'target' => 'messages',
            'folder' => 'sent',
        );
    }

    /**
     * @Route("/reply/{id}", name="message_reply")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function replyAction($id)
    {
        $thread = MessageQuery::create()->findThreadForUser($id, $this->getUser());

        $newMessage = new Model\Message\Message();
        $newMessageType = new FormType\MessageType();
        $newMessageType->setFileStorage($this->container->get('zerebral.file_storage')->getFileStorage('local'));

        $form = $this->createForm($newMessageType, $newMessage);
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
                $readInThreadCount++;
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
            'folder' => null,
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
        $newMessageType->setFileStorage($this->container->get('zerebral.file_storage')->getFileStorage('local'));

        $form = $this->createForm($newMessageType, $newMessage);
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
            'folder' => null,
            'unreadCount' => $unreadCount,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/suggest-user/", name="ajax_message_suggest_user")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function suggestUserAction()
    {
        $name = $this->getRequest()->get('username');
        if ($name) {
            $userList = \Zerebral\BusinessBundle\Model\User\UserQuery::create()->findForSuggestByNameForUser($name, $this->getUser());
            $response = array();
            if (count($userList)) {
                foreach ($userList as $user) {
                    $response[] = array(
                        'id' => $user->getId(),
                        'name' => $user->getFullName(),
                    );
                }
            }
            return new JsonResponse(array('success' => true, 'users' => $response));
        }

        throw new \Symfony\Component\HttpKernel\Exception\HttpException(404, 'User name is empty.');
    }

//     /**
//     * @Route("/delete/{id}", name="assignment_delete")
//     * @Route("/delete/{id}/{courseId}", name="course_assignment_delete")
//     * @ParamConverter("assignment", options={"mapping": {"id": "id"}})
//     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
//     *
//     * @SecureParam(name="assignment", permissions="DELETE")
//     * @PreAuthorize("hasRole('ROLE_TEACHER')")
//     * @Template()
//     */
//    public function deleteAction(Model\Assignment\Assignment $assignment, Model\Course\Course $course = null)
//    {
//        $assignment->delete();
//        $this->setFlash(
//            'delete_assignment_success',
//                'Assignment <b>' . $assignment->getName() . '</b> has been successfully deleted from course ' . $assignment->getCourse()->getName() . '.'
//        );
//        if ($course) {
//            return $this->redirect($this->generateUrl('course_assignments', array('id' => $course->getId())));
//        } else {
//            return $this->redirect($this->generateUrl('assignments'));
//        }
//
//    }
}
