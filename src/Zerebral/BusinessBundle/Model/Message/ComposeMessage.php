<?php
namespace Zerebral\BusinessBundle\Model\Message;

use Zerebral\BusinessBundle\Model\Message\om\BaseMessage;

class ComposeMessage extends BaseMessage
{
    public $message;
    public $recipients;

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
}
