<?php

namespace Zerebral\BusinessBundle\Model\Course;

use Zerebral\BusinessBundle\Model\Course\om\BaseCourseQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedItemPeer;
use Zerebral\BusinessBundle\Model\Feed\FeedCommentPeer;

class CourseQuery extends BaseCourseQuery
{

    /**
     * Find courses by Teacher id
     *
     * @param integer $id
     * @return Course|Course[]|mixed the result, formatted by the current formatter
     */
    public function findByTeacher($id){
       return $this->findByCreatedBy($id);
    }

    /**
     * @param \Zerebral\BusinessBundle\Model\User\Student $roleUser
     */
    public function findByRoleUser($roleUser)
    {
        $c = new \Criteria();
        $c->addJoin(CoursePeer::ID, FeedItemPeer::COURSE_ID, \Criteria::LEFT_JOIN);
        $c->addJoin(FeedItemPeer::ID, FeedCommentPeer::FEED_ITEM_ID, \Criteria::LEFT_JOIN);

        $c->addGroupByColumn(FeedCommentPeer::ID);

        $c->addAsColumn('commentsCount', 'COUNT('.\Zerebral\BusinessBundle\Model\Feed\FeedCommentPeer::ID.')');
//        $c->addSelectColumn('COUNT('.FeedCommentPeer::ID.') as commentsCount');
//        $c->addSelectModifier()

        return $roleUser->getCourses();

    }
}
