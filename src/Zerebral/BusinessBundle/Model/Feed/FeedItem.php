<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedItem;

class FeedItem extends BaseFeedItem
{
    public function preInsert(\PropelPDO $con = null) {
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }
}
