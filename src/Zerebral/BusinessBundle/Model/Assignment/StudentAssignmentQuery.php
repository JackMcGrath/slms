<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseStudentAssignmentQuery;

class StudentAssignmentQuery extends BaseStudentAssignmentQuery
{
    public function findStudentsByAssignmentId($assignmentId)
    {
        $this->leftJoinAssignment();
        $this->filterByAssignmentId($assignmentId);
        //$this->filterByIsSubmitted(true);
        $this->innerJoinStudentAssignmentFile();
        $this->leftJoinStudent();
        $this->leftJoin('Student.User User');

        $this->add('due_at', "IF(assignments.due_at > '".date('Y-m-d H:i:s')."', student_assignments.is_submitted=1, TRUE)", \Criteria::CUSTOM);

        $this->withColumn('COUNT(StudentAssignmentFile.file_id)', 'filesCount');
        $this->groupBy('student_assignments.id');

        return $this;
    }

    public function filterByAssignmentAndStudent($student, $assignment)
    {
        $this->filterByStudent($student);
        $this->filterByAssignment($assignment);
        //$this->filterByIsSubmitted(true);
        return $this;
    }

    public function findTodayCount()
    {
        $this->leftJoinAssignment();
        $this->groupBy('student_assignments.student_id');
        $this->withColumn('COUNT(assignments.id)', 'assignmentsCount');
        $this->where("DATE(assignments.due_at)='" . date('Y-m-d') . "'");
        return $this;
    }
}
