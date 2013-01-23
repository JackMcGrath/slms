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
        if ($this->getCommentsCount() == 0) {
            return array();
        }
        $criteria = new \Criteria();
        $criteria->setLimit($this->defaultCommentsCount)->setOffset($this->getCommentsCount() - $this->defaultCommentsCount);//->addAscendingOrderByColumn(FeedCommentPeer::ID);
        return $this->getFeedComments($criteria);
    }
}
