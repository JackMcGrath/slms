<?php
namespace Zerebral\BusinessBundle\Model\Message;

use Zerebral\BusinessBundle\Model\Message\om\BaseMessage;

class ComposeMessage
{
    public $message;
    public $recipients;

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }

    public function save()
    {
        foreach ($this->getRecipients() as $recipient) {
            $recipientMessage = $this->getMessage()->copy();
            foreach($this->getMessage()->getFiles() as $file) {
                $recipientMessage->addFile($file->copy());
            }
            $recipientMessage->setToId($recipient->getId());
            $recipientMessage->setUserId($recipient->getId());
            $recipientMessage->save();
        }
    }
}
