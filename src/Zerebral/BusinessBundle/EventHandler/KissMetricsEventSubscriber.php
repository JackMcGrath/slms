<?php

namespace Zerebral\BusinessBundle\EventHandler;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;
use Zerebral\CommonBundle\KissMetrics\KissMetrics;
use Zerebral\BusinessBundle\Model as Model;

class KissMetricsEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /**
     * @var KissMetrics
     */
    private $kissMetrics = null;

    public function __construct(KissMetrics $kissMetrics)
    {
        $this->setKissMetrics($kissMetrics);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'courses.insert.post' => 'createCourseEvent',
            'feed_items.insert.post' => 'createFeedEvent',
        );
    }

    public function createCourseEvent(ModelEvent $event)
    {
        /** @var $course Model\Course\Course */
        $course = $event->getModel();
        $this->getKissMetrics()->createEvent('course', array(
            'name' => $course->getName(),
            'subject_area' => $course->getDiscipline()->getName(),
            'grade_level' => $course->getGradeLevel()->getName(),
        ));
    }

    public function createFeedEvent(ModelEvent $event)
    {
        /** @var $feedItem Model\Feed\FeedItem */
        $feedItem = $event->getModel();
        $this->getKissMetrics()->createEvent('feed', array(
            'type' => $feedItem->getFeedContent()->getType(),
            'course' => $feedItem->getCourseId() ? $feedItem->getCourse()->getName() : 'global',
        ));
    }

    /**
     * @param \Zerebral\CommonBundle\KissMetrics\KissMetrics $kissMetrics
     */
    public function setKissMetrics($kissMetrics)
    {
        $this->kissMetrics = $kissMetrics;
    }

    /**
     * @return \Zerebral\CommonBundle\KissMetrics\KissMetrics
     */
    public function getKissMetrics()
    {
        return $this->kissMetrics;
    }
}
