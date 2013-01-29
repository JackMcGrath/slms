<?php

namespace Zerebral\BusinessBundle\Model\Feed;

use Zerebral\BusinessBundle\Model\Feed\om\BaseFeedItemQuery;

use Zerebral\BusinessBundle\Model\Course\CourseStudentPeer;
use Zerebral\BusinessBundle\Model\Course\CourseTeacherPeer;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer;
use Zerebral\BusinessBundle\Model\Course\CoursePeer;

class FeedItemQuery extends BaseFeedItemQuery
{
    public function getGlobalFeed(\Zerebral\BusinessBundle\Model\User\User $user)
    {
        if ($user->isStudent()) {
            $this->addJoin(FeedItemPeer::COURSE_ID, CourseStudentPeer::COURSE_ID, \Criteria::LEFT_JOIN);
            $this->addAnd(CourseStudentPeer::STUDENT_ID, $user->getStudent()->getId(), \Criteria::EQUAL);
            $this->addOr(FeedItemPeer::COURSE_ID, null, \Criteria::ISNULL);

            $this->addJoin(FeedItemPeer::ASSIGNMENT_ID, StudentAssignmentPeer::ASSIGNMENT_ID, \Criteria::LEFT_JOIN);
            $this->addAnd(StudentAssignmentPeer::STUDENT_ID, $user->getStudent()->getId(), \Criteria::EQUAL);
            $this->addOr(FeedItemPeer::ASSIGNMENT_ID, null, \Criteria::ISNULL);
        } else {
            $this->addJoin(FeedItemPeer::COURSE_ID, CourseTeacherPeer::COURSE_ID, \Criteria::LEFT_JOIN);
            $this->addAnd(CourseTeacherPeer::TEACHER_ID, $user->getTeacher()->getId(), \Criteria::EQUAL);
            $this->addOr(FeedItemPeer::COURSE_ID, null, \Criteria::ISNULL);
        }

        $relatedUsers = $user->getRelatedUsers();
        $ids = array();
        foreach($relatedUsers as $relatedUser) {
            $ids[] = $relatedUser->getId();
        }

        $this->addAnd(FeedItemPeer::CREATED_BY, $ids, \Criteria::IN);
        $this->addDescendingOrderByColumn(FeedItemPeer::ID);

        $this->joinWith('Assignment', \Criteria::LEFT_JOIN);
        $this->joinWith('Course', \Criteria::LEFT_JOIN);
        $this->joinWith('FeedContent', \Criteria::LEFT_JOIN);
        $this->addJoin(FeedItemPeer::ID, FeedCommentPeer::FEED_ITEM_ID, \Criteria::LEFT_JOIN);
        $this->joinWith('User', \Criteria::LEFT_JOIN);

        $this->addAsColumn('commentsCount', 'COUNT(' . FeedCommentPeer::ID . ')');
        $this->addGroupByColumn(FeedItemPeer::ID);

        return $this;
    }

    public function getCourseFeed(\Zerebral\BusinessBundle\Model\Course\Course $course, \Zerebral\BusinessBundle\Model\User\User $user)
    {
        if ($user->isStudent()) {
            $this->addJoin(FeedItemPeer::ASSIGNMENT_ID, StudentAssignmentPeer::ASSIGNMENT_ID, \Criteria::LEFT_JOIN);
            $this->addAnd(StudentAssignmentPeer::STUDENT_ID, $user->getStudent()->getId(), \Criteria::EQUAL);
            $this->addOr(FeedItemPeer::ASSIGNMENT_ID, null, \Criteria::ISNULL);
        }
        $this->addJoin(FeedItemPeer::COURSE_ID, CoursePeer::ID, \Criteria::LEFT_JOIN);
        $this->addAnd(CoursePeer::ID, $course->getId(), \Criteria::EQUAL);
        $this->addDescendingOrderByColumn(FeedItemPeer::ID);

        $this->joinWith('Assignment', \Criteria::LEFT_JOIN);
        //$this->joinWith('Course', \Criteria::LEFT_JOIN);
        $this->joinWith('FeedContent', \Criteria::LEFT_JOIN);
        $this->addJoin(FeedItemPeer::ID, FeedCommentPeer::FEED_ITEM_ID, \Criteria::LEFT_JOIN);
        $this->joinWith('User', \Criteria::LEFT_JOIN);

        $this->addAsColumn('commentsCount', 'COUNT(' . FeedCommentPeer::ID . ')');
        $this->addGroupByColumn(FeedItemPeer::ID);

        return $this;
    }

    public function getFeedItemsAfter($lastItemId)
    {
        return $this->clearOrderByColumns()->addDescendingOrderByColumn(FeedItemPeer::ID)->filterById($lastItemId, \Criteria::GREATER_THAN);
    }

    public function getFeedItemsBefore($lastItemId)
    {
        return $this->clearOrderByColumns()->addDescendingOrderByColumn(FeedItemPeer::ID)->filterById($lastItemId, \Criteria::LESS_THAN);
    }
}
