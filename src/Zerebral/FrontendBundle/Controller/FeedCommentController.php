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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return \Symfony\Component\HttpFoundation\Response|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     */
    public function saveAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {
        if (!$this->isAjaxRequest()) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Direct calls are not allowed');
        }

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
//            $content = $this->render('ZerebralFrontendBundle:Feed:feedCommentBlock.html.twig', array('feedType' => $feedType, 'comment' => $feedComment))->getContent();
//            return new JsonResponse(array('has_errors' => false, 'content' => $content, 'lastCommentId' => $feedComment->getId()));
            $lastCommentId = $this->getRequest()->get('lastCommentId', 0);
            $comments = FeedCommentQuery::create()->filterNewer($feedItem, $lastCommentId)->find();
            $content = '<hr />start save. Showing comments from ' . $lastCommentId . ' ASC<br />';
            if (count($comments) > 0) {
                foreach ($comments as $comment) {
                    $content .= $this->render('ZerebralFrontendBundle:Feed:feedCommentBlock.html.twig', array('feedType' => $feedType, 'comment' => $comment))->getContent();
                }
                $lastCommentId = $comments->getLast()->getId();
            }
            $content .= 'end save<hr />';

            return new JsonResponse(array('has_errors' => false, 'lastCommentId' => $lastCommentId, 'content' => $content));
        }

        return new FormJsonResponse($feedCommentForm);
    }

    /**
     * @Route("/remove/{feedCommentId}", name="ajax_feed_remove_comment")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedComment $feedComment
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedComment", options={"mapping": {"feedCommentId": "id"}})
     */
    public function removeAction(\Zerebral\BusinessBundle\Model\Feed\FeedComment $feedComment)
    {
        if (!$this->isAjaxRequest()) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Direct calls are not allowed');
        }

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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     */
    public function indexAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {

        if (!$this->isAjaxRequest()) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Direct calls are not allowed');
        }

        $page = 1;
        $lastCommentId = $this->getRequest()->get('lastCommentId', 0);

        /** @var $commentsQuery \Zerebral\BusinessBundle\Model\Feed\FeedCommentQuery */
        $commentsQuery = FeedCommentQuery::create()->filterOlder($feedItem, $lastCommentId);

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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     */
    public function checkoutAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {
        if (!$this->isAjaxRequest()) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Direct calls are not allowed');
        }

        $lastCommentId = $this->getRequest()->get('lastCommentId', 0);
        $comments = FeedCommentQuery::create()->filterNewer($feedItem, $lastCommentId)->find();
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
