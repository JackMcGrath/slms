<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseStudentAssignmentQuery;

class StudentAssignmentQuery extends BaseStudentAssignmentQuery
{
    public function findStudentsByAssignmentId($assignmentId)
    {
        $this->filterByAssignmentId($assignmentId);
        $this->filterByIsSubmitted(true);
        $this->leftJoinFileReferences();
        $this->withColumn('COUNT(FileReferences.file_id)', 'filesCount');
        $this->groupBy('FileReferences.file_id');

        return $this;
    }
}
