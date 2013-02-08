<?php

namespace Zerebral\BusinessBundle\EventHandler;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;
use Zerebral\CommonBundle\KissMetrics\KissMetrics;


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
            'users.insert.post' => 'newUser',
            'courses.insert.post' => 'newCourse',
            'assignments.insert.post' => 'newAssignment',
            'messages.insert.post' => 'newMessage',
            'student_attendance.insert.post' => 'newStudentAttendance'
        );
    }

    public function newUser(ModelEvent $event)
    {
        /** @var $user \Zerebral\BusinessBundle\Model\User\User */
        $user = $event->getModel();

        $this->getKissMetrics()->createEvent('Sign up', array('role' => $user->getRole()));
    }

    public function newCourse(ModelEvent $event)
    {
        /** @var $course \Zerebral\BusinessBundle\Model\Course\Course */
        $course = $event->getModel();

        $this->getKissMetrics()->createEvent('New course');
    }

    public function newAssignment(ModelEvent $event)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        $assignment = $event->getModel();
        $this->getKissMetrics()->createEvent('New assignment', array('test' => 'test'));
    }

    public function newMessage(ModelEvent $event)
    {
        /** @var $message \Zerebral\BusinessBundle\Model\Message\Message */
        $message = $event->getModel();

        //do not handle copy of message
        if ($message->getUserId() != $message->getFromId()) {
            $this->getKissMetrics()->createEvent('New message');
        }
    }

    public function newStudentAttendance(ModelEvent $event)
    {
        /** @var $studentAttendance \Zerebral\BusinessBundle\Model\Attendance\StudentAttendance */
        $studentAttendance = $event->getModel();

        $this->getKissMetrics()->createEvent('Attendance', array('status' => $studentAttendance->getStatus()));
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