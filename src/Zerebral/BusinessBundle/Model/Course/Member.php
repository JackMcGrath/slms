<?php

namespace Zerebral\BusinessBundle\Model\Course;
use Symfony\Component\Validator\ExecutionContext;


class Member
{
    private $emailList;

    /**
     * @param mixed $emailList
     */
    public function setEmailList($emailList)
    {
        if(is_string($emailList)){
            $this->emailList = $this->getFromString($emailList);
        }else{
            $this->emailList = $emailList;
        }
    }

    private function getFromString($emailList){
        $results = explode(' ', preg_replace('/\s\s+/', ' ', trim($emailList)));
        return $results;
    }

    /**
     * get array of email addresses for send invites
     *
     * @return mixed
     */
    public function getEmailList()
    {
        return $this->emailList;
    }

    public function isEmailListValid(ExecutionContext $context)
    {
        foreach($this->getEmailList() as $email){
            if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email)){
                $context->addViolationAtSubPath('emailList', 'Please check all email addresses.', array(), null);
            }
        }
    }
}
