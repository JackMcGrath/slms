<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedItem;

class FeedItem extends BaseFeedItem
{
    private $defaultCommentsCount = 3;

    public function preInsert(\PropelPDO $con = null)
    {
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }

    public function getLastFeedComments()
    {
        $criteria = new \Criteria();
        $criteria->setLimit($this->defaultCommentsCount)->setOffset($this->countFeedComments() - $this->defaultCommentsCount)->addAscendingOrderByColumn('created_at');
        return $this->getFeedComments($criteria);
    }
}
