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

    public static function filterOlder($feedItem, $lastCommentId)
    {
        return self::create()->clearOrderByColumns()
            ->addDescendingOrderByColumn(FeedCommentPeer::ID)
            ->filterByFeedItem($feedItem)
            ->filterById($lastCommentId, \Criteria::LESS_THAN)
            ->find();
    }
}
