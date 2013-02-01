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
    public function filterByRoleUser($roleUser)
    {
        $this->leftJoinFeedItem();
        $this->leftJoin('FeedItem.FeedComment FeedComments');

        $this->joinWith('Assignment', \Criteria::LEFT_JOIN);
        $this->joinWith('CourseTeacher', \Criteria::LEFT_JOIN);

        $this->addGroupByColumn(CoursePeer::ID);


        if ($roleUser->getUser()->isTeacher()) {
            $this->filterByTeacher($roleUser);
            $this->addJoinCondition('Assignment', 'Assignment.teacher_id='.$roleUser->getId());
        } else {
            $this->filterByStudent($roleUser);
            $this->leftJoin('Assignment.StudentAssignment StudentAssignments');
            $this->addJoinCondition('StudentAssignments', '`StudentAssignments`.student_id='.$roleUser->getId());
            $this->withColumn('COUNT(DISTINCT `StudentAssignments`.id)', 'studentAssignmentsCount');
        }

        $this->withColumn('COUNT(`FeedComments`.id)', 'commentsCount');
        $this->withColumn('COUNT(DISTINCT assignments.id)', 'assignmentsCount');

        $this->withColumn('GROUP_CONCAT(DISTINCT DATE(assignments.due_at) SEPARATOR ",")', 'dueDates');


        return $this;

    }
}
