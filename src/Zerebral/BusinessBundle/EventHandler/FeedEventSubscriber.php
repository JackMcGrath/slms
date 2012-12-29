<?php

namespace Zerebral\BusinessBundle\EventHandler;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;

use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\Feed\FeedContent;

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
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        $assignment = $event->getModel();

        $feedContent = new FeedContent();
        $feedContent->setType('assignment');

        $feedItem = new FeedItem();
        $feedItem->setAssignment($assignment);
        $feedItem->setCourse($assignment->getCourse());
        $feedItem->setCreatedBy($assignment->getTeacher()->getUser()->getId());
        $feedItem->setFeedContent($feedContent);

        $feedItem->save();
    }

}
