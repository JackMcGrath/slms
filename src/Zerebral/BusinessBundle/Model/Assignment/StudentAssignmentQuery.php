<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseStudentAssignmentQuery;

class StudentAssignmentQuery extends BaseStudentAssignmentQuery
{
    public function findStudentsByAssignmentId($assignmentId)
    {
        $this->filterByAssignmentId($assignmentId);
        $this->filterByIsSubmitted(true);
        $this->innerJoinFileReferences();
        $this->leftJoinStudent();
        $this->leftJoin('Student.User User');

        $this->withColumn('COUNT(FileReferences.file_id)', 'filesCount');
        $this->groupBy('student_assignments.id');

        return $this;
    }
}
