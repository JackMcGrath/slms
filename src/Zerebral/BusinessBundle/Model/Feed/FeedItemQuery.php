<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedItemQuery;

use Zerebral\BusinessBundle\Model\Course\CourseStudentPeer;
use Zerebral\BusinessBundle\Model\Course\CourseTeacherPeer;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer;

class FeedItemQuery extends BaseFeedItemQuery
{
    public function getItemsForUser(\Zerebral\BusinessBundle\Model\User\User $user)
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

        return parent::create(null, $criteria)->find();
    }
}
