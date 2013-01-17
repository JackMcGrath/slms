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
     * @Route("/save", name="ajax_add_feed_item")
     * @param \Zerebral\BusinessBundle\Model\Course\Course
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     *
     * TODO: add is ajax validation
     */
    public function saveAction()
    {
        $feedItemFormType = new FormType\FeedItemType();
        $feedItemForm = $this->createForm($feedItemFormType, null);


        $feedItemForm->bind($this->getRequest());

        if ($feedItemForm->isValid()) {

            $feedItem = $feedItemForm->getData();
            $feedItem->setCreatedBy($this->getUser()->getId());
            $feedItem->save();

            $content = $this->render('ZerebralFrontendBundle:Feed:feedItemBlock.html.twig', array('feedItem' => $feedItem, 'isGlobal' => false))->getContent();
            return new JsonResponse(array('has_errors' => false, 'content' => $content, 'lastItemId' => $feedItem->getId()));
        }

        return new FormJsonResponse($feedItemForm);
    }


    /**
     * @Route("/remove/{feedItemId}", name="ajax_remove_feed_item")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("feedItem", options={"mapping": {"feedItemId": "id"}})
     *
     * TODO: add is ajax validation
     */
    public function removeAction(\Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem)
    {
        if (($this->getUser()->getId() == $feedItem->getCreatedBy()) && ($feedItem->getFeedContent()->getType() != 'assignment')) {
            $feedItem->delete();
            return new JsonResponse(array());
        } else {
            return new JsonResponse(array('message' => 'You can\'t delete other feed items or assignments items'), 403);
        }
    }

    /**
     * @Route("/checkout/{courseId}", name="ajax_checkout_items", defaults={"courseId" = null})
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     *
     * TODO: add is ajax validation
     */
    public function checkoutAction(Course $course = null)
    {
        $lastItemId = $this->getRequest()->get('lastItemId', 0);
        if (is_null($course)) {
            $query = \Zerebral\BusinessBundle\Model\Feed\FeedItemQuery::getGlobalFeed($this->getUser());
        } else {
            $query = \Zerebral\BusinessBundle\Model\Feed\FeedItemQuery::getCourseFeed($course, $this->getUser());
        }

        $items = $query->filterNewer($lastItemId)->find();

        $content = '';
        if (count($items) > 0) {
            foreach ($items as $item) {
                $content .= $this->render('ZerebralFrontendBundle:Feed:feedItemBlock.html.twig', array('feedItem' => $item, 'isGlobal' => is_null($course)))->getContent();
            }
            $lastItemId = $items->getFirst()->getId();
        }

        return new JsonResponse(array('success' => true, 'lastItemId' => $lastItemId, 'content' => $content));
    }
}
