<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignment;

class Assignment extends BaseAssignment
{

    public function __construct()
    {
        $this->setMaxPoints(100);
        parent::__construct();
    }


    public function preDelete(\PropelPDO $con = null)
    {
        foreach ($this->getFiles() as $file) {
            $file->delete();
        }
        return parent::preDelete($con);
    }

    /** @return \Zerebral\BusinessBundle\Model\Feed\FeedItem */
    public function getFeedItem()
    {
        return $this->getFeedItems()->getFirst();
    }

    public function hasNotificationByType($type)
    {
        $c = new \Criteria();
        $c->add('type', $type);
        return (bool)$this->getNotifications($c)->count();
    }
}
