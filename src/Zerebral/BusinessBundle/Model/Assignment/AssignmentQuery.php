<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignmentQuery;

class AssignmentQuery extends BaseAssignmentQuery
{
    public function getCourseAssignmentsDueDate(\Zerebral\BusinessBundle\Model\Course\Course $course = null, $ongoing = null, \Zerebral\BusinessBundle\Model\User\Teacher $teacher = null)
    {
        if ($teacher) {
            $this->filterByTeacher($teacher);
        }
        if ($ongoing === false) {
            $this->add('assignments.due_at', null, \Criteria::ISNOTNULL);
        } else if ($ongoing === true) {
            $this->add('assignments.due_at', null, \Criteria::ISNULL);
        }
        $this->leftJoinStudentAssignment();
        $this->leftJoinCourse();

        $this->leftJoin('StudentAssignment.FileReferences FileReference');
        $this->add('student_assignments.id', null, \Criteria::ISNOTNULL);

        if ($course) {
            $this->filterByCourseId($course->getId());
        }

        $this->withColumn('COUNT(DISTINCT IF(student_assignments.is_submitted = 1 AND `FileReference`.file_id is not null, student_assignments.id, null))', 'completedCount');
        $this->withColumn('COUNT(DISTINCT student_assignments.id) - COUNT(DISTINCT IF(student_assignments.is_submitted = 1 AND `FileReference`.file_id is not null, student_assignments.id, null))', 'remainingCount');
        $this->withColumn('COUNT(DISTINCT IF(student_assignments.is_submitted = 1, `FileReference`.file_id , null))', 'filesCount');

        $this->addGroupByColumn('assignments.id');

        return $this;
    }

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
        $this->where("DATE_FORMAT(due_at, '%Y-%m-%d %H:%i')>'" . date('Y-m-d H:i', strtotime('-1 hour')) . "'");
        $this->where("StudentAssignment.is_submitted=1");
        $this->groupBy('assignments.id');
        return $this;
    }
}
