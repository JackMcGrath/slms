<?php
namespace Zerebral\CommonBundle\Component\Calendar;

class Calendar extends \CalendR\Calendar
{
    private $currentMonth;

    public function __construct($time, $eventProvider)
    {
        $this->currentMonth = $this->getMonth(new \DateTime(date('Y-m-01', $time)));
        $this->getEventManager()->addProvider('', $eventProvider);
    }

    public function getCurrentMonth()
    {
        return $this->currentMonth;
    }

    public function getCurrentEvents()
    {
        return $this->getEvents($this->getCurrentMonth());
    }


//    public function getNextMonth()
//    {
//        return $this->getMonth(new \DateTime(strtotime("+1 month")));
//    }
//
//    public function getCurrentMonth()
//    {
//        return $this->getMonth(new \DateTime());
//    }
//
//    public function getEvents()
//    {
//        return $this->getEventManager()->getProviders();
//    }
}
