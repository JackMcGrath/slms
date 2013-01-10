<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedContent;
use Zerebral\CommonBundle\Component\FeedContentFetcher\FeedContentFetcher;

class FeedContent extends BaseFeedContent
{
    public function preInsert(\PropelPDO $con = null)
    {
        $feedContentFetcher = new FeedContentFetcher($this);
        $this->setLinkTitle($feedContentFetcher->getLinkTitle());
        $this->setLinkDescription($feedContentFetcher->getLinkDescription());
        $this->setLinkThumbnailUrl($feedContentFetcher->getLinkThumbmnailUrl());
        return parent::preInsert($con);
    }

}
