<?php
namespace Zerebral\BusinessBundle\Calendar\EventProviders;

use CalendR\Event\Provider\ProviderInterface;
use \CalendR\Event\Event;

class CourseAssignmentEventsProvider implements ProviderInterface
{
    private $events = array();

    public function __construct($collection)
    {
        if ($collection) {
            foreach ($collection as $object) {
                $this->setEvent($object);
            }
        }
    }

    public function getEvents(\DateTime $begin, \DateTime $end, array $options = array())
    {
        /*
         Returns an array of events here. $options is the second argument of
         $factory->getEvents(), so you can filter your event on anything (Calendar id/slug ?)
        */
        return $this->events;
    }


    private function setEvent(\Zerebral\BusinessBundle\Model\Assignment\Assignment $model)
    {
        if ($model->getDueAt())
            $this->events[] = new Event($model->getCourse()->getName().': '. $model->getName(), $model->getDueAt(), $model->getDueAt());
    }
}
