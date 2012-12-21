<?php

namespace Zerebral\BusinessBundle\Model\Course;


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
}
