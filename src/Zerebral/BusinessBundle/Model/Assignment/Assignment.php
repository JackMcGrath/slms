<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignment;

class Assignment extends BaseAssignment
{
    /**
     * Separate filed for due_at day
     * @var string
     */
    public $due_at_date;

    /**
     * Separate field for due_at time
     * @var string
     */
    private $due_at_time;

    /**
     * @param string $due_at_date
     */
    public function setDueAtDate($due_at_date)
    {
        $this->due_at_date = $due_at_date;
    }

    /**
     * @return string
     */
    public function getDueAtDate()
    {
        return $this->due_at_date;
    }

    /**
     * @param string $due_at_time
     */
    public function setDueAtTime($due_at_time)
    {
        $this->due_at_time = $due_at_time;
    }

    /**
     * @return string
     */
    public function getDueAtTime()
    {
        return $this->due_at_time;
    }

    public function preSave(\PropelPDO $con = null)
    {
        if(! $this->getDueAt()){
            $this->setDueAt($this->getDueAtDate() ." ". $this->getDueAtTime());
        }
        return parent::preSave($con);
    }

}
