<?php

namespace Zerebral\BusinessBundle\EventHandler;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;

use Zerebral\BusinessBundle\Model\Notification\Notification;
use Zerebral\BusinessBundle\Model\Notification\NotificationPeer;
use Zerebral\BusinessBundle\Model\File\FileReferences;

class NotificationEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'assignments.insert.post' => 'createAssignment',
            'assignments.update.post' => 'updateAssignment',
            'courses.update.post' => 'updateCourse',
            'course_materials.insert.post' => 'createMaterial',
            'file_references.insert.post' => 'createFile', //New Assignment File,
            'attendance.save.post' => 'updateAttendance',
            'feed_items.insert.post' => 'createFeed'
        );
    }

    public function createAssignment(ModelEvent $event)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        $assignment = $event->getModel();

        foreach ($assignment->getStudentAssignments() as $student) {
            $notification = new Notification();
            $notification->setUserId($student->getStudent()->getUserId());
            $notification->setType(NotificationPeer::TYPE_ASSIGNMENT_CREATE);
            $notification->setAssignment($assignment);
            $notification->setCourse($assignment->getCourse());
            $notification->setCreatedBy($assignment->getTeacher()->getUserId());
            $notification->save();
        }
    }

    public function updateAssignment(ModelEvent $event)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        $assignment = $event->getModel();


        foreach ($assignment->getStudentAssignments() as $student) {
            $notification = new Notification();
            $notification->setUserId($student->getStudent()->getUserId());
            $notification->setType(NotificationPeer::TYPE_ASSIGNMENT_UPDATE);
            $notification->setAssignment($assignment);
            $notification->setCourse($assignment->getCourse());
            $notification->setCreatedBy($assignment->getTeacher()->getUserId());
            $notification->save();
        }

    }

    public function updateCourse(ModelEvent $event)
    {
        /** @var $course \Zerebral\BusinessBundle\Model\Course\Course */
        $course = $event->getModel();

        foreach ($course->getCourseStudents() as $student) {
            $notification = new Notification();
            $notification->setUserId($student->getStudent()->getUserId());
            $notification->setType(NotificationPeer::TYPE_COURSE_UPDATE);
            $notification->setCourse($course);
            $notification->setCreatedBy($course->getTeachers()->getFirst()->getUserId());
            $notification->save();
        }
    }

    public function createMaterial(ModelEvent $event)
    {
        /** @var $material \Zerebral\BusinessBundle\Model\Material\CourseMaterial */
        $material = $event->getModel();

        if ($material->isNew()) {
            foreach ($material->getCourse()->getStudents() as $student) {
                $notification = new Notification();
                $notification->setUserId($student->getStudent()->getUserId());
                $notification->setType(NotificationPeer::TYPE_MATERIAL_CREATE);
                $notification->setCourse($material->getCourse());
                $notification->setCreatedBy($material->getCourse()->getTeachers()->getFirst()->getUserId());
                $notification->save();
            }
        }
    }

    public function createFile(ModelEvent $event)
    {
        /** @var $fileReference \Zerebral\BusinessBundle\Model\File\FileReferences */
        $fileReference = $event->getModel();

        if ($fileReference->getreferenceType() == \Zerebral\BusinessBundle\Model\File\FileReferencesPeer::REFERENCE_TYPE_ASSIGNMENT) {
            foreach ($fileReference->getAssignmentReferenceId()->getStudents() as $student) {
                $notification = new Notification();
                $notification->setUserId($student->getStudent()->getUserId());
                $notification->setType(NotificationPeer::TYPE_ASSIGNMENT_FILE_CREATE);
                $notification->setCourse($fileReference->getassignmentReferenceId()->getCourse());
                $notification->setAssignment($fileReference->getassignmentReferenceId());
                $notification->setCreatedBy($fileReference->getassignmentReferenceId()->getCourse()->getTeachers()->getFirst()->getUserId());
                $notification->setParam('file_id',$fileReference->getfileId());
                $notification->save();
            }
        }
    }

    public function updateAttendance(ModelEvent $event)
    {
        /** @var $attendance \Zerebral\BusinessBundle\Model\Attendance\Attendance */
        $attendance = $event->getModel();

        foreach ($attendance->getStudentAttendances() as $studentAttendance) {
            if ($studentAttendance != \Zerebral\BusinessBundle\Model\Attendance\om\BaseStudentAttendancePeer::STATUS_PRESENT) {
                $notification = new Notification();
                $notification->setUserId($studentAttendance->getStudent()->getUserId());
                $notification->setType(NotificationPeer::TYPE_ATTENDANCE_STATUS);
                $notification->setCourse($attendance->getCourse());
                $notification->setCreatedBy($attendance->getTeacher()->getUserId());
                $notification->setParam('status', $studentAttendance->getStatus());
                $notification->setParam('date', $attendance->getDate('Y-m-d'));
                $notification->save();
            }
        }
    }

    public function createFeed(ModelEvent $event)
    {

    }
}
