<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedContent;
use Zerebral\CommonBundle\Component\FeedContentFetcher\FeedContentFetcher;

class FeedContent extends BaseFeedContent
{
    public function preInsert(\PropelPDO $con = null)
    {
        $feedContentFetcher = new FeedContentFetcher($this);
        $feedContentFetcher->fetch();
        return parent::preInsert($con);
    }

}
