<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedItemQuery;

use Zerebral\BusinessBundle\Model\Course\CourseStudentPeer;
use Zerebral\BusinessBundle\Model\Course\CourseTeacherPeer;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer;
use Zerebral\BusinessBundle\Model\Course\CoursePeer;

class FeedItemQuery extends BaseFeedItemQuery
{
    public static function getGlobalFeed(\Zerebral\BusinessBundle\Model\User\User $user)
    {
        $criteria = new \Criteria();

        if ($user->isStudent()) {

            $criteria->addJoin(FeedItemPeer::COURSE_ID, CourseStudentPeer::COURSE_ID, \Criteria::LEFT_JOIN);
            $criteria->addAnd(CourseStudentPeer::STUDENT_ID, $user->getStudent()->getId(), \Criteria::EQUAL);
            $criteria->addOr(FeedItemPeer::COURSE_ID, null, \Criteria::ISNULL);

            $criteria->addJoin(FeedItemPeer::ASSIGNMENT_ID, StudentAssignmentPeer::ASSIGNMENT_ID, \Criteria::LEFT_JOIN);
            $criteria->addAnd(StudentAssignmentPeer::STUDENT_ID, $user->getStudent()->getId(), \Criteria::EQUAL);
            $criteria->addOr(FeedItemPeer::ASSIGNMENT_ID, null, \Criteria::ISNULL);
        } else {
            $criteria->addJoin(FeedItemPeer::COURSE_ID, CourseTeacherPeer::COURSE_ID, \Criteria::LEFT_JOIN);
            $criteria->addAnd(CourseTeacherPeer::TEACHER_ID, $user->getTeacher()->getId(), \Criteria::EQUAL);
            $criteria->addOr(FeedItemPeer::COURSE_ID, null, \Criteria::ISNULL);
        }
        $criteria->addDescendingOrderByColumn('created_at');

        return parent::create(null, $criteria);
    }

    public static function getCourseFeed(\Zerebral\BusinessBundle\Model\Course\Course $course, \Zerebral\BusinessBundle\Model\User\User $user)
    {
        $criteria = new \Criteria();

        if ($user->isStudent()) {
            $criteria->addJoin(FeedItemPeer::ASSIGNMENT_ID, StudentAssignmentPeer::ASSIGNMENT_ID, \Criteria::LEFT_JOIN);
            $criteria->addAnd(StudentAssignmentPeer::STUDENT_ID, $user->getStudent()->getId(), \Criteria::EQUAL);
            $criteria->addOr(FeedItemPeer::ASSIGNMENT_ID, null, \Criteria::ISNULL);
        }
        $criteria->addJoin(FeedItemPeer::COURSE_ID, CoursePeer::ID, \Criteria::LEFT_JOIN);
        $criteria->addAnd(CoursePeer::ID, $course->getId(), \Criteria::EQUAL);
        $criteria->addDescendingOrderByColumn('created_at');
        return parent::create(null, $criteria);
    }

    public function filterNewer($lastItemId)
    {
        return $this->clearOrderByColumns()->addDescendingOrderByColumn(FeedItemPeer::ID)->filterById($lastItemId, \Criteria::GREATER_THAN);
    }
}
