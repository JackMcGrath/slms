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
}
