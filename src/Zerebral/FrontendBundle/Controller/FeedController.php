<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Zerebral\CommonBundle\HttpFoundation\FormJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;


use Zerebral\FrontendBundle\Form\Type as FormType;

use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\Feed\FeedComment;
use Zerebral\BusinessBundle\Model\Course\Course;

use Zerebral\BusinessBundle\Model\Feed\FeedCommentQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedItemQuery;

/**
 * @Route("/feed")
 */
class FeedController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/save", name="ajax_add_feed_item")
     * @internal param $ \Zerebral\BusinessBundle\Model\Course\Course
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     */
    public function saveAction()
    {
        if (!$this->isAjaxRequest()) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Direct calls are not allowed');
        }

        $feedItemFormType = new FormType\FeedItemType();
        $feedItemForm = $this->createForm($feedItemFormType, null);


        $feedItemForm->bind($this->getRequest());

        if ($feedItemForm->isValid()) {

            /** @var FeedItem $feedItem  */
            $feedItem = $feedItemForm->getData();
            $feedItem->setCreatedBy($this->getUser()->getId());
            $feedItem->save();

            $course = $feedItem->getCourse();
            $lastItemId = $this->getRequest()->get('lastItemId', 0);

            $query = FeedItemQuery::create();
            $query = (is_null($course)) ? $query->getGlobalFeed($this->getUser()) : $query->getCourseFeed($course, $this->getUser());
            $feedItems = $query->getFeedItemsAfter($lastItemId)->find();

            $content = '';
            if (count($feedItems) > 0) {
                foreach ($feedItems as $item) {
                    $content .= $this->render('ZerebralFrontendBundle:Feed:feedItemBlock.html.twig', array('feedItem' => $item, 'isGlobal' => is_null($course)))->getContent();
                }
                $lastItemId = $feedItems->getFirst()->getId();
            }
            return new JsonResponse(array('has_errors' => false, 'content' => $content, 'lastItemId' => $lastItemId));
        }

        return new FormJsonResponse($feedItemForm);
    }


    /**
     * @Route("/remove/{feedItemId}", name="ajax_remove_feed_item")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     */
    public function removeAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {
        if (!$this->isAjaxRequest()) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Direct calls are not allowed');
        }

        if (($this->getUser()->getId() == $feedItem->getCreatedBy()) && ($feedItem->getFeedContent()->getType() != 'assignment')) {
            $feedItem->delete();
            return new JsonResponse(array());
        } else {
            return new JsonResponse(array('message' => 'You can\'t delete other feed items or assignments items'), 403);
        }
    }

    /**
     * Realtime update handler
     *
     * @Route("/checkout/{courseId}", name="ajax_checkout_items", defaults={"courseId" = null})
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     */
    public function checkoutAction(Course $course = null)
    {

//        if (!$this->isAjaxRequest()) {
//            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Direct calls are not allowed');
//        }

        $lastItemId = $this->getRequest()->get('lastItemId', 0);

        $query = FeedItemQuery::create();
        $query = (is_null($course)) ? $query->getGlobalFeed($this->getUser()) : $query->getCourseFeed($course, $this->getUser());
        $feedItems = $query->getFeedItemsAfter($lastItemId)->find();

        $content = '';
        if (count($feedItems) > 0) {
            foreach ($feedItems as $item) {
                $content .= $this->render('ZerebralFrontendBundle:Feed:feedItemBlock.html.twig', array('feedItem' => $item, 'isGlobal' => is_null($course)))->getContent();
            }
            $lastItemId = $feedItems->getFirst()->getId();
        }


        $lastCommentsIds = $this->getRequest()->get('lastIds', array());
        $comments = FeedCommentQuery::create()->getCommentsTreeAfter($lastCommentsIds)->find();
        $sortedByItemComments = array();
        foreach($comments as $comment) {
            if (!isset($sortedByItemComments[$comment->getFeedItemId()])) {
                $sortedByItemComments[$comment->getFeedItemId()] = array('lastCommentId' => 0, 'comments' => array());
            }
            $sortedByItemComments[$comment->getFeedItemId()]['comments'][] = $comment;
            if ($sortedByItemComments[$comment->getFeedItemId()]['lastCommentId'] < $comment->getId()) {
                $sortedByItemComments[$comment->getFeedItemId()]['lastCommentId'] = $comment->getId();
            }
        }

        foreach ($sortedByItemComments as $feedItemId => $item) {
            $commentsContent = '';
            foreach ($item['comments'] as $comment) {
                $commentsContent .= $this->render('ZerebralFrontendBundle:Feed:feedCommentBlock.html.twig', array('feedType' => 'course', 'comment' => $comment))->getContent();
            }
            $sortedByItemComments[$feedItemId]['content'] = $commentsContent;
            $sortedByItemComments[$feedItemId]['count'] = count($item['comments']);
            unset($sortedByItemComments[$feedItemId]['comments']);
        }

        return new JsonResponse(array('success' => true, 'lastIds' => $lastCommentsIds, 'lastItemId' => $lastItemId, 'content' => $content, 'comments' => $sortedByItemComments));
    }

    /**
     * Show more updates link handler
     *
     * @Route("/{courseId}", name="ajax_load_more_items", defaults={"courseId" = null})
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     */
    public function indexAction(Course $course = null)
    {
        if (!$this->isAjaxRequest()) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Direct calls are not allowed');
        }

        $lastItemId = $this->getRequest()->get('lastItemId', 0);

        $query = FeedItemQuery::create();
        $query = (is_null($course)) ? $query->getGlobalFeed($this->getUser()) : $query->getCourseFeed($course, $this->getUser());
        $feedItems = $query->getFeedItemsBefore($lastItemId)->limit(10)->find();

        $content = '';
        if (count($feedItems) > 0) {
            foreach ($feedItems as $item) {
                $content .= $this->render('ZerebralFrontendBundle:Feed:feedItemBlock.html.twig', array('feedItem' => $item, 'isGlobal' => is_null($course)))->getContent();
            }
            $lastItemId = $feedItems->getLast()->getId();
        }

        return new JsonResponse(array('success' => true, 'lastItemId' => $lastItemId, 'content' => $content, 'loadedCount' => count($feedItems)));
    }
}
