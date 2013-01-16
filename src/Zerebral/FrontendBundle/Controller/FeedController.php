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

/**
 * @Route("/feed")
 */
class FeedController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/save/{courseId}", name="ajax_course_add_feed_item")
     * @param \Zerebral\BusinessBundle\Model\Course\Course
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     *
     * TODO: add is ajax validation
     */
    public function saveItemAction(\Zerebral\BusinessBundle\Model\Course\Course $course)
    {
        $feedItemFormType = new FormType\FeedItemType();
        $feedItemForm = $this->createForm($feedItemFormType, null);


        $feedItemForm->bind($this->getRequest());

        if ($feedItemForm->isValid()) {

            $feedItem = $feedItemForm->getData();
            $feedItem->setCreatedBy($this->getUser()->getId());
            $course->addFeedItem($feedItem);
            $course->save();

            $content = $this->render('ZerebralFrontendBundle:Feed:feedItemBlock.html.twig', array('feedItem' => $feedItem, 'isGlobal' => false))->getContent();
            return new JsonResponse(array('has_errors' => false, 'content' => $content));
        }

        return new FormJsonResponse($feedItemForm);
    }

    /**
     * @Route("/save-global", name="ajax_global_add_feed_item")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     *
     * TODO: add is ajax validation
     */
    public function saveGlobalItemAction()
    {
        $feedItemFormType = new FormType\FeedItemType();
        $feedItemForm = $this->createForm($feedItemFormType, null);


        $feedItemForm->bind($this->getRequest());

        if ($feedItemForm->isValid()) {

            $feedItem = $feedItemForm->getData();
            $feedItem->setCreatedBy($this->getUser()->getId());
            $feedItem->save();

            $content = $this->render('ZerebralFrontendBundle:Feed:feedItemBlock.html.twig', array('feedItem' => $feedItem, 'isGlobal' => true))->getContent();
            return new JsonResponse(array('has_errors' => false, 'content' => $content));
        }

        return new FormJsonResponse($feedItemForm);
    }

    /**
     * @Route("/remove/{feedItemId}", name="ajax_feed_remove_feed_item")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     *
     * TODO: add is ajax validation
     */
    public function removeItemAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {
        if (($this->getUser()->getId() == $feedItem->getCreatedBy()) && ($feedItem->getFeedContent()->getType() != 'assignment')) {
            $feedItem->delete();
            return new JsonResponse(array());
        } else {
            return new JsonResponse(array('message' => 'You can\'t delete other feed items or assignments items'), 403);
        }
    }
}
