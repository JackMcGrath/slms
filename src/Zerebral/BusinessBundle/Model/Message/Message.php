<?php

namespace Zerebral\BusinessBundle\Model\Message;

use Zerebral\BusinessBundle\Model\Message\om\BaseMessage;

class Message extends BaseMessage
{
    public $isMarkedAsRead;
    public $toName;

    public function markAsRead()
    {
        $this->setIsRead(true);
        $this->isMarkedAsRead = true;
        $this->save();
        return true;
    }

    // name "copy" already used in parent class
    protected function makeACopy()
    {
        $sentCopy = new Message();
        $this->copyInto($sentCopy, true, true);

        $sentCopy->setUserId($this->getFromId());
        $sentCopy->setIsRead(true);

        $sentCopy->save();
    }

    public function preInsert(\PropelPDO $con = null)
    {
        if (!$this->getThreadId()) {
            $this->setThreadId($this->getFromId() . time());
        }
        // @todo use listener?
        $this->setCreatedAt(date('Y-m-d H:i:s'));

        return parent::preInsert($con);
    }

    public function postInsert(\PropelPDO $con = null)
    {
        if ($this->getFromId() !== $this->getUserId()) {
            $this->makeACopy();
        }

        parent::postInsert($con);
    }

    public function getShortBody()
    {
        return strip_tags($this->body);
    }

}
