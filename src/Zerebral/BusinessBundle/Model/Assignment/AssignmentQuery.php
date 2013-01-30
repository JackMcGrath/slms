<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignmentQuery;

class AssignmentQuery extends BaseAssignmentQuery
{
    public function findToday()
    {
        $this->add('DATE(due_at)', date('Y-m-d'));
        return $this;
    }

    public function findTodayCountForTeacher()
    {
        $this->findToday();
        $this->withColumn('COUNT(assignments.id)', 'assignmentsCount');
        $this->groupBy('teacher_id');
        return $this;
    }

    public function findInCompletedNow()
    {
        $this->leftJoinStudentAssignment();
        $this->where("DATE_FORMAT(due_at, '%Y-%m-%d %H:%i')>'" . date('Y-m-d H:i', strtotime('-1 hour')) . "'");
        $this->where("DATE_FORMAT(due_at, '%Y-%m-%d %H:%i')<='" . date('Y-m-d H:i') . "'");
        $this->where("StudentAssignment.is_submitted=0");
        $this->withColumn('COUNT(student_assignments.id)', 'missedSubmissionsCount');
        $this->groupBy('assignments.id');
        return $this;
    }

    public function findCompleteNow()
    {
        $this->leftJoinStudentAssignment();
        $this->where("DATE_FORMAT(due_at, '%Y-%m-%d %H:%i')>='" . date('Y-m-d H:i', strtotime('-1 hour')) . "'");
        $this->groupBy('assignments.id');
        $this->having('count(IF(student_assignments.is_submitted=0, student_assignments.id, null))=0');
        return $this;
    }

    public function findSortedByCourse($course)
    {
        $this->filterByCourse($course);
        $this->leftJoinStudentAssignment();
        $this->addAscendingOrderByColumn('assignments.due_at');
        $this->groupBy('assignments.id');

        return $this;
    }

    public function buildForList()
    {
//        $this->leftJoinStudentAssignment();
        $this->joinWith('StudentAssignment', \Criteria::LEFT_JOIN);
        $this->leftJoinCourse();
        $this->leftJoinFeedItem();

        $this->leftJoinFeedItem();
        $this->leftJoin('FeedItem.FeedComment FeedComments');
        $this->leftJoin('StudentAssignment.StudentAssignmentFile StudentAssignmentFile');
        $this->add('student_assignments.id', null, \Criteria::ISNOTNULL);

        $this->withColumn('COUNT(DISTINCT IF(student_assignments.is_submitted = 1, student_assignments.id, null))', 'completedCount');
        $this->withColumn('COUNT(DISTINCT student_assignments.id) - COUNT(DISTINCT IF(student_assignments.is_submitted = 1, student_assignments.id, null))', 'remainingCount');
        $this->withColumn('COUNT(DISTINCT IF(student_assignments.is_submitted = 1, `StudentAssignmentFile`.file_id , null))', 'filesCount');
        $this->withColumn('COUNT(DISTINCT student_assignments.id)', 'studentsCount');

        $this->withColumn('COUNT(DISTINCT `FeedComments`.id)', 'commentsCount');

        $this->addGroupByColumn('assignments.id');

        return $this;
    }

    /**
     * @param \Zerebral\BusinessBundle\Model\User\User $user
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @param null $ongoing
     * @return array|mixed|\PropelObjectCollection
     */
    public static function filterByUserAndDueDate(\Zerebral\BusinessBundle\Model\User\User $user, \Zerebral\BusinessBundle\Model\Course\Course $course = null, $ongoing = null)
    {
        $assignments = AssignmentQuery::create()->buildForList();
        if ($user->isTeacher())
            $assignments->filterByTeacher($user->getTeacher());
        else {
            $assignments->filterByStudent($user->getStudent());
            $assignments->add("student_assignments.student_id", $user->getStudent()->getId(), \Criteria::EQUAL);
        }

        if ($ongoing === false) {
            $assignments->add('assignments.due_at', null, \Criteria::ISNOTNULL);
        } else if ($ongoing === true) {
            $assignments->add('assignments.due_at', null, \Criteria::ISNULL);
        }

        if ($user->isTeacher() && $ongoing == true) {
            $assignments->addDescendingOrderByColumn('remainingCount');
        } else {
            $assignments->addDescendingOrderByColumn('assignments.due_at');
        }

        if ($course) {
            $assignments->filterByCourseId($course->getId());
        }

        return $assignments;
    }

    public function findDraftByTeacher($teacher, \Zerebral\BusinessBundle\Model\Course\Course $course = null)
    {
        $this->filterByTeacher($teacher);
        if ($course) {
            $this->filterByCourse($course);
        }

        $this->leftJoinStudentAssignment();
        $this->add('student_assignments.id', null, \Criteria::ISNULL);
        $this->addDescendingOrderByColumn('due_at');
        return $this->find();
    }
}
