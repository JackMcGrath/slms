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
    private $gradingIsModified = false;
    private $solutionIsSubmitted = false;
    private $isFirstGrade = false;

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
            'student_attendance.insert.post' => 'newStudentAttendance',
            'student_assignments.update.pre' => 'changeStudentAssignment', //grading or solution
            'student_assignments.update.post' => 'changeStudentAssignment', //grading of solution
            'feed_items.insert.post' => 'newFeedItem',
            'feed_comments.insert.post' => 'newFeedItemComment',
            'course_materials.insert.post' => 'newSyllabus',
            'courses.invite_student' => 'inviteStudentsToCourse'
        );
    }

    public function newUser(ModelEvent $event)
    {
        /** @var $user \Zerebral\BusinessBundle\Model\User\User */
        $user = $event->getModel();

        $this->getKissMetrics()->createEvent('Sign up', array('new_user_role' => $user->getRole(), 'new_user_name' => $user->getFullName()));
    }

    public function newCourse(ModelEvent $event)
    {
        /** @var $course \Zerebral\BusinessBundle\Model\Course\Course */
        $course = $event->getModel();

        $this->getKissMetrics()->createEvent('New course', array(
            'course_name' => $course->getName(),
            'course_subject' => $course->getDiscipline()->getName(),
            'course_grade_level' => $course->getGradeLevel()->getName()
        ));
    }

    public function newAssignment(ModelEvent $event)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        $assignment = $event->getModel();
        $this->getKissMetrics()->createEvent('New assignment', array('assignment_name' => $assignment->getName()));
    }

    public function newMessage(ModelEvent $event)
    {
        /** @var $message \Zerebral\BusinessBundle\Model\Message\Message */
        $message = $event->getModel();

        //do not handle copy of message
        if ($message->getUserId() != $message->getFromId()) {
            $this->getKissMetrics()->createEvent('New message', array(
                'message_from_role' => $message->getUserRelatedByFromId()->getRole(),
                'message_to_role' => $message->getUserRelatedByToId()->getRole(),
                'message_id' => $message->getId())
            );
        }
    }

    public function newStudentAttendance(ModelEvent $event)
    {
        /** @var $studentAttendance \Zerebral\BusinessBundle\Model\Attendance\StudentAttendance */
        $studentAttendance = $event->getModel();

        $this->getKissMetrics()->createEvent('Attendance', array(
            'attendance_status' => $studentAttendance->getStatus(),
            'attendance_course_name' => $studentAttendance->getAttendance()->getCourse()->getName(),
            'attendance_student_name' => $studentAttendance->getStudent()->getFullName(),
            'attendance_date' => $studentAttendance->getAttendance()->getDate())
        );
    }

    public function changeStudentAssignment(ModelEvent $event)
    {
        /** @var $studentAssignment \Zerebral\BusinessBundle\Model\Assignment\StudentAssignment */
        $studentAssignment = $event->getModel();

        $today = new \DateTime();

        if ($this->gradingIsModified) {
            $this->getKissMetrics()->createEvent('Grading pass/fail', array(
                'grading_status' => $studentAssignment->getGradeStatus(),
                'grading_course' => $studentAssignment->getAssignment()->getName(),
                'grading_student' => $studentAssignment->getStudent()->getFullName()
            ));
        }
        if ($this->isFirstGrade) {
            $this->getKissMetrics()->createEvent('First grade after assignment due date', array(
                'first_grade_days_after_due_date' => $today->diff($studentAssignment->getAssignment()->getDueAt())->days
            ));
        }
        if ($this->solutionIsSubmitted) {
            $this->getKissMetrics()->createEvent('Solution submitted', array(
                'submit_solution_before_due_date' => (!$studentAssignment->isReadyForGrading() && $studentAssignment->getIsSubmitted()), //isReadyForGrading due_date < current date,
                'submit_solution_days_before_due_date' => $today->diff($studentAssignment->getAssignment()->getDueAt())->days
            ));
        }

        if ($studentAssignment->isColumnModified('student_assignments.grading')) {
            $this->gradingIsModified = true;
            $this->isFirstGrade = !\Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery::create()->filterByAssignmentId($studentAssignment->getAssignmentId())->filterByGrading(null, \Criteria::ISNOTNULL)->exists();
        }
        if ($studentAssignment->isColumnModified('student_assignments.is_submitted') && $studentAssignment->getIsSubmitted()) {
            $this->solutionIsSubmitted = true;
        }
    }

    public function newFeedItem(ModelEvent $event)
    {
        /** @var $feed \Zerebral\BusinessBundle\Model\Feed\FeedItem */
        $feed = $event->getModel();

        $this->getKissMetrics()->createEvent('feed post', array(
            'feed_post_author_role' => $feed->getUser()->getRole(),
            'feed_post_assignment' => $feed->getAssignmentId() ? $feed->getAssignment()->getName() : '',
            'feed_post_course' => $feed->getCourseId() ? $feed->getCourse()->getName() : '',
        ));
    }

    public function newFeedItemComment(ModelEvent $event)
    {
        /** @var $feed \Zerebral\BusinessBundle\Model\Feed\FeedComment */
        $feed = $event->getModel();

        $this->getKissMetrics()->createEvent('feed post comment', array(
            'feed_post_author_role' => $feed->getUser()->getRole(),
            'feed_post_assignment' => $feed->getFeedItem()->getAssignmentId() ? $feed->getFeedItem()->getAssignment()->getName() : '',
            'feed_post_course' => $feed->getFeedItem()->getCourseId() ? $feed->getFeedItem()->getCourse()->getName() : '',
        ));
    }

    public function newSyllabus(ModelEvent $event)
    {
        /** @var $syllabus \Zerebral\BusinessBundle\Model\Material\CourseMaterial */
        $syllabus = $event->getModel();
        $file = $syllabus->getFile();
        $size = round($file->getSize() / pow(1024, 2), 2); //pow(1024, 2) - 2 means Megabytes

        $this->getKissMetrics()->createEvent('syllabus', array(
            'syllabus_course_name' => $syllabus->getCourse()->getName(),
            'syllabus_size' => $size,
            'syllabus_mime_type' => $file->getMimeType()
        ));
    }

    public function inviteStudentsToCourse(ModelEvent $event)
    {
        /** @var $course \Zerebral\BusinessBundle\Model\Course\Course */
        $course = $event->getModel();
        $this->getKissMetrics()->createEvent('invite to course', array(
            'course_student_course_name' => $course->getName()
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