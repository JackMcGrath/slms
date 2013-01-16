<?php

namespace Zerebral\BusinessBundle\Model\Notification;

use Zerebral\BusinessBundle\Model\Notification\om\BaseNotificationQuery;

class NotificationQuery extends BaseNotificationQuery
{
    public function findUnreadByUserId($userId)
    {
        $this->filterByUserId($userId);
        $this->filterByIsRead(false);
        $this->orderBy('created_at', \Criteria::DESC);
        return $this;
    }

    public function findAllByUserId($userId)
    {
        $this->filterByUserId($userId);
        $this->orderBy('created_at', \Criteria::DESC);
        return $this;
    }

}
