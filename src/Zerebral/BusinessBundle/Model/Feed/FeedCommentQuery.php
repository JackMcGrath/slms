<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedCommentQuery;

use Zerebral\BusinessBundle\Model\Feed\FeedCommentPeer;

class FeedCommentQuery extends BaseFeedCommentQuery
{
    public function filterByFeedItem($feedItem, $comparison = null)
    {
        $filter = parent::filterByFeedItem($feedItem, $comparison);
        return $filter->orderByCreatedAt();
    }

    public function filterOlder($feedItem, $lastCommentId)
    {
        return $this->clearOrderByColumns()
            ->addDescendingOrderByColumn(FeedCommentPeer::ID)
            ->filterByFeedItem($feedItem)
            ->filterById($lastCommentId, \Criteria::LESS_THAN);
    }

    public function filterNewer($feedItem, $lastCommentId)
    {
        return $this->clearOrderByColumns()
            ->addAscendingOrderByColumn(FeedCommentPeer::ID)
            ->filterByFeedItem($feedItem)
            ->filterById($lastCommentId, \Criteria::GREATER_THAN);
    }

    public function getNewComments($parameters)
    {
        $this->clearOrderByColumns()->addAscendingOrderByColumn(FeedCommentPeer::ID);
        $criteria = new \Criteria();
        foreach($parameters as $feedItemId => $feedCommentId) {
            $itemCondition = $criteria->getNewCriterion(FeedCommentPeer::FEED_ITEM_ID, $feedItemId, \Criteria::EQUAL);
            $commentCondition = $criteria->getNewCriterion(FeedCommentPeer::ID, $feedCommentId, \Criteria::GREATER_THAN);
            $itemCondition->addAnd($commentCondition);
            $this->addOr($itemCondition);
        }
        return $this;

    }
}
