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

/**
 * @Route("/feed")
 */
class FeedController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/add-comment/{feedItemId}", name="ajax_feed_add_comment")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
     * @return \Symfony\Component\HttpFoundation\Response|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     */
    public function addFeedCommentAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {
        $feedCommentFormType = new FormType\FeedCommentType();
        $feedCommentForm = $this->createForm($feedCommentFormType, null);

        $feedCommentForm->bind($this->getRequest());

        if ($feedCommentForm->isValid()) {

            /** @var $feedComment FeedComment */
            $feedComment = $feedCommentForm->getData();
            $feedComment->setCreatedBy($this->getUser()->getId());
            $feedItem->addFeedComment($feedComment);
            $feedItem->save();

            $feedType = $this->getRequest()->get('feedType', 'assignment');

            return $this->render('ZerebralFrontendBundle:Feed:feedCommentBlock.html.twig', array('feedType' => $feedType, 'comment' => $feedComment));
        }

        return new FormJsonResponse($feedCommentForm, 500);
    }

    /**
     * @Route("/remove-comment/{feedCommentId}", name="ajax_feed_remove_comment")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedComment $feedComment
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedComment", options={"mapping": {"feedCommentId": "id"}})
     */
    public function removeFeedCommentAction(\Zerebral\BusinessBundle\Model\Feed\FeedComment $feedComment) {
        if ($this->getUser()->getId() == $feedComment->getCreatedBy()) {
            $feedComment->delete();
            return new JsonResponse(array());
        } else {
            return new JsonResponse(array('message' => 'You can\'t delete other comments'), 403);
        }
    }

    /**
     * @Route         ("/add-feed-item/{courseId}", name="ajax_course_add_feed_item")
     *
     * @param         \Zerebral\BusinessBundle\Model\Course\Course
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     */
    public function addFeedItemAction(\Zerebral\BusinessBundle\Model\Course\Course $course)
    {
        $feedItemFormType = new FormType\FeedItemType();
        $feedItemForm = $this->createForm($feedItemFormType, null);


        $feedItemForm->bind($this->getRequest());

        if ($feedItemForm->isValid()) {

            $feedItem = $feedItemForm->getData();
            $feedItem->setCreatedBy($this->getUser()->getId());
            $course->addFeedItem($feedItem);
            $course->save();

            return $this->render('ZerebralFrontendBundle:Feed:feedItemBlock.html.twig', array('feedItem' => $feedItem));
        }

        return new FormJsonResponse($feedItemForm, 500);
    }

    /**
     * @Route("/remove-feed-item/{feedItemId}", name="ajax_feed_remove_feed_item")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     */
    public function removeFeedItemAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem) {
        if (($this->getUser()->getId() == $feedItem->getCreatedBy()) && ($feedItem->getFeedContent()->getType() != 'assignment')) {
            $feedItem->delete();
            return new JsonResponse(array());
        } else {
            return new JsonResponse(array('message' => 'You can\'t delete other feed items or assignments items'), 403);
        }
    }
}
