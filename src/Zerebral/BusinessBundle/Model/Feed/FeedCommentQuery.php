<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedCommentQuery;

use Zerebral\BusinessBundle\Model\Feed\FeedCommentPeer;

class FeedCommentQuery extends BaseFeedCommentQuery
{
    public function filterByFeedItem($feedItem, $comparison = null)
    {
        $filter = parent::filterByFeedItem($feedItem, $comparison);
        $filter->joinWith('FeedContent');
        return $filter->orderById();
    }

    public function getCommentsBefore($feedItem, $lastCommentId)
    {
        return $this->filterByFeedItem($feedItem)
            ->filterById($lastCommentId, \Criteria::LESS_THAN)
            ->clearOrderByColumns()->addDescendingOrderByColumn(FeedCommentPeer::ID);
    }

    public function getCommentsAfter($feedItem, $lastCommentId)
    {
        return $this->filterByFeedItem($feedItem)
            ->filterById($lastCommentId, \Criteria::GREATER_THAN)
            ->clearOrderByColumns()->addAscendingOrderByColumn(FeedCommentPeer::ID);
    }

    public function getCommentsTreeAfter($parameters)
    {
        if (count($parameters) == 0) {
            return array();
        }
        $this->clearOrderByColumns()->addAscendingOrderByColumn(FeedCommentPeer::ID);
        $criteria = new \Criteria();
        foreach($parameters as $feedItemId => $feedCommentId) {
            $itemCondition = $criteria->getNewCriterion(FeedCommentPeer::FEED_ITEM_ID, $feedItemId, \Criteria::EQUAL);
            $commentCondition = $criteria->getNewCriterion(FeedCommentPeer::ID, $feedCommentId, \Criteria::GREATER_THAN);
            $itemCondition->addAnd($commentCondition);
            $this->addOr($itemCondition);
        }
        $this->joinWith('FeedContent');
        return $this;

    }
}
