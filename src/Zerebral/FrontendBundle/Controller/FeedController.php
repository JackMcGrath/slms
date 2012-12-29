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

use Zerebral\BusinessBundle\Model\Feed\FeedComment;
use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\Feed\FeedContent;

/**
 * @Route("/feed")
 */
class FeedController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/add-comment/{feedItemId}", name="ajax_feed_add_comment")
     * @param \Zerebral\BusinessBundle\Model\Feed\FeedItem $feedItem
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
            return new JsonResponse(array(
                'redirect' => $this->generateUrl(
                    'assignment_view',
                    array(
                        'id' => $feedItem->getAssignment()->getId()
                    )
                )
            ));
        }

        return new FormJsonResponse($feedCommentForm);
    }
}
