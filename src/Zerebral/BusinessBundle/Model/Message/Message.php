<?php

namespace Zerebral\BusinessBundle\Model\Message;

use Zerebral\BusinessBundle\Model\Message\om\BaseMessage;
use \Zerebral\BusinessBundle\Model\File\FileReferences;

class Message extends BaseMessage
{
    public $isMarkedAsRead;
    public $toName;

    protected function doAddFile($file)
    {
        $fileReferences = new FileReferences();
        $fileReferences->setFile($file);
        $fileReferences->setreferenceType('message');
        $this->addFileReferences($fileReferences);
    }

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

        foreach ($sentCopy->getFileReferencess() as $reference) {
            $reference->setassignmentReferenceId(null);
        }

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

}
