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
use Zerebral\BusinessBundle\Model\Feed\FeedCommentPeer;

/**
 * @Route("/feed/comments")
 */
class FeedCommentController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/save/{feedItemId}", name="ajax_feed_add_comment")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
     * @return \Symfony\Component\HttpFoundation\Response|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     *
     * TODO: add is ajax validation
     */
    public function saveAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
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
            $content = $this->render('ZerebralFrontendBundle:Feed:feedCommentBlock.html.twig', array('feedType' => $feedType, 'comment' => $feedComment))->getContent();
            return new JsonResponse(array('has_errors' => false, 'content' => $content));
        }

        return new FormJsonResponse($feedCommentForm);
    }

    /**
     * @Route("/remove/{feedCommentId}", name="ajax_feed_remove_comment")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedComment $feedComment
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedComment", options={"mapping": {"feedCommentId": "id"}})
     *
     * TODO: add is ajax validation
     */
    public function removeAction(\Zerebral\BusinessBundle\Model\Feed\FeedComment $feedComment)
    {
        if ($this->getUser()->getId() == $feedComment->getCreatedBy()) {
            $feedComment->delete();
            return new JsonResponse(array());
        } else {
            return new JsonResponse(array('message' => 'You can\'t delete other comments'), 403);
        }
    }


    /**
     * @Route("/{feedItemId}", name="ajax_load_more_comments")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     *
     * TODO: add is ajax validation
     */
    public function indexAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {
        $page = 1;
        $lastCommentId = $this->getRequest()->get('lastCommentId', 0);

        /** @var $commentsQuery \Zerebral\BusinessBundle\Model\Feed\FeedCommentQuery */
        $commentsQuery = FeedCommentQuery::filterOlder($feedItem, $lastCommentId);

        /** @var $commentsPaginator \PropelModelPager */
        $commentsPaginator = $commentsQuery->paginate($page, 10);

        $comments = $commentsPaginator->getResults();
        $feedType = $this->getRequest()->get('feedType', 'course');
        $content = $this->render('ZerebralFrontendBundle:Feed:feedCommentBlock.html.twig', array('feedType' => $feedType, 'comment' => $comments))->getContent();
        return new JsonResponse(array('success' => true, 'lastCommentId' => $comments->getLast()->getId(), 'loadedCount' => count($comments), 'content' => $content));
    }

    /**
     * @Route("/checkout/{feedItemId}", name="ajax_checkout_comments")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     *
     * TODO: add is ajax validation
     */
    public function checkoutAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {
        $lastCommentId = $this->getRequest()->get('lastCommentId', 0);
        $comments = FeedCommentQuery::filterNewer($feedItem, $lastCommentId);
        $content = '';
        if (count($comments) > 0) {
            foreach ($comments as $comment) {
                $content .= $this->render('ZerebralFrontendBundle:Feed:feedCommentBlock.html.twig', array('feedType' => 'assignment', 'comment' => $comment))->getContent();
            }
            $lastCommentId = $comments->getLast()->getId();
        }

        return new JsonResponse(array('success' => true, 'lastCommentId' => $lastCommentId, 'content' => $content));
    }
}
