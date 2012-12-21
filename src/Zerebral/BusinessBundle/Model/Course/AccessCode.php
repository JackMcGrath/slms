<?php

namespace Zerebral\BusinessBundle\Model\Course;


class AccessCode
{

    private $accessCode;

    public function setAccessCode($accessCode)
    {
        $this->accessCode = $accessCode;
    }

    public function getAccessCode()
    {
        return $this->accessCode;
    }
}
