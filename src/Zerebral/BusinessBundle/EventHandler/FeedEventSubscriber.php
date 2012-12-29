<?php

namespace Zerebral\BusinessBundle\EventHandler;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;


class FeedEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'assignments.insert.post' => 'sendNewAssignment'
        );
    }

    public function sendNewAssignment(ModelEvent $event)
    {
//        $feedItem = new FeedItem();
//        $feedItem->setAssignment($event->getModel());
//        $feedItem->setCourse($event->getModel()->getCourse());
//        $feedItem->setUser($event->getModel()->getTeacher());
//        $feedItem->save();
    }

}
