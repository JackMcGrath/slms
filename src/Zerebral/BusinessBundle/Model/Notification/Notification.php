<?php

namespace Zerebral\BusinessBundle\Model\Notification;

use Zerebral\BusinessBundle\Model\Notification\om\BaseNotification;

class Notification extends BaseNotification
{
    public function preInsert(\PropelPDO $con = null)
    {
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }
}
