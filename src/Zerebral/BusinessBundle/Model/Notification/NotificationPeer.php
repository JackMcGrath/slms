<?php

namespace Zerebral\BusinessBundle\Model\Notification;

use Zerebral\BusinessBundle\Model\Notification\om\BaseNotificationPeer;

class NotificationPeer extends BaseNotificationPeer
{
    public static function createAssignmentComplete($assignments)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        foreach ($assignments as $assignment) {
            if (!$assignment->hasNotificationByType(self::TYPE_ASSIGNMENT_COMPLETE)) {
                $notification = new Notification();
                $notification->setUserId($assignment->getTeacher()->getUserId());
                $notification->setType(self::TYPE_ASSIGNMENT_COMPLETE);
                $notification->setAssignment($assignment);
                $notification->save();
            }
        }
    }

    public static function createAssignmentInCompleted($assignments)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        foreach ($assignments as $assignment) {
            $notification = new Notification();
            $notification->setUserId($assignment->getTeacher()->getUserId());
            $notification->setType(self::TYPE_ASSIGNMENT_INCOMPLETE);
            $notification->setAssignment($assignment);
            $notification->setParam('missedSubmissionsCount', $assignment->getVirtualColumn('missedSubmissionsCount'));
            $notification->save();
        }
    }

    public static function createAssignmentDueDateSingleStudent($assignments)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        foreach ($assignments as $assignment) {
            foreach ($assignment->getStudents() as $student) {
                $notification = new Notification();
                $notification->setUserId($student->getStudent()->getUserId());
                $notification->setType(self::TYPE_ASSIGNMENT_DUE_SINGLE_STUDENT);
                $notification->setAssignment($assignment);
                $notification->setCourse($assignment->getCourse());
                $notification->save();
            }

        }
    }

    public static function createAssignmentDueDateMultipleStudent($students)
    {
        /** @var $student \Zerebral\BusinessBundle\Model\Assignment\StudentAssignment */
        foreach ($students as $student) {
            $notification = new Notification();
            $notification->setUserId($student->getStudent()->getUserId());
            $notification->setType(self::TYPE_ASSIGNMENT_DUE_MULTIPLE_STUDENT);
            $notification->setAssignment($student->getAssignment());
            $notification->setParam('assignmentsCount', $student->getVirtualColumn('assignmentsCount'));
            $notification->save();
        }
    }

    public static function createAssignmentDueDateSingleTeacher($assignments)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        foreach ($assignments as $assignment) {
            $notification = new Notification();
            $notification->setUserId($assignment->getTeacher()->getUserId());
            $notification->setType(self::TYPE_ASSIGNMENT_DUE_SINGLE_TEACHER);
            $notification->setAssignment($assignment);
            $notification->setCourse($assignment->getCourse());
            $notification->save();
        }
    }

    public static function createAssignmentDueDateMultipleTeacher($assignments)
    {
        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        foreach ($assignments as $assignment) {
            $notification = new Notification();
            $notification->setUserId($assignment->getTeacher()->getUserId());
            $notification->setType(self::TYPE_ASSIGNMENT_DUE_MULTIPLE_TEACHER);
            $notification->setAssignment($assignment);
            $notification->setParam('assignmentsCount', $assignment->getVirtualColumn('assignmentsCount'));
            $notification->save();
        }
    }
}
