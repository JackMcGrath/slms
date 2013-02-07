<?php

namespace Zerebral\BusinessBundle\EventHandler;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;

class KissmetricsEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public $webTracker;
    public $sessionTracker;

    public function __construct($webTracker, $sessionTracker)
    {
        $this->setWebTracker($webTracker);
        $this->setSessionTracker($sessionTracker);
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
            'student_attendance' => 'newStudentAttendance'
        );
    }

    public function newUser(ModelEvent $event)
    {
        /** @var $user \Zerebral\BusinessBundle\Model\User\User */
        $user = $event->getModel();

        $this->getSessionTracker()->addRecords('Registration', array('role' => $user->getRole()));
    }

    public function newCourse(ModelEvent $event)
    {
        /** @var $course \Zerebral\BusinessBundle\Model\Course\Course */
        $course = $event->getModel();

        $this->getSessionTracker()->addRecords('new_course');
    }

    public function newAssignment(ModelEvent $event)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        $assignment = $event->getModel();
        $this->getSessionTracker()->addRecords('new_assignment', array('test' => 'test'));
    }

    public function newMessage(ModelEvent $event)
    {
        /** @var $message \Zerebral\BusinessBundle\Model\Message\Message */
        $message = $event->getModel();

        //do not handle copy of message
        if ($message->getUserId() != $message->getFromId()) {
            $this->getSessionTracker()->addRecords('new_message');
        }
    }

    public function newStudentAttendance(ModelEvent $event)
    {
        /** @var $studentAttendance \Zerebral\BusinessBundle\Model\Attendance\StudentAttendance */
        $studentAttendance = $event->getModel();

        $this->getSessionTracker()->addRecords('Attendance', array('status' => $studentAttendance->getStatus()));
    }

    public function setWebTracker($webTracker)
    {
        $this->webTracker = $webTracker;
    }

    /**
     * @return \Tirna\KissmetricsBundle\Tracker\WebTracker
     */
    public function getWebTracker()
    {
        return $this->webTracker;
    }

    public function setSessionTracker($sessionTracker)
    {
        $this->sessionTracker = $sessionTracker;
    }

    /**
     * @return \Tirna\KissmetricsBundle\Tracker\WebTracker\SessionTracker
     */
    public function getSessionTracker()
    {
        return $this->sessionTracker;
    }

}
