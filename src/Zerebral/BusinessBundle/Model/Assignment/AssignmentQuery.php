<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignmentQuery;

class AssignmentQuery extends BaseAssignmentQuery
{
    public function getCourseAssignmentsDueDate(\Zerebral\BusinessBundle\Model\Course\Course $course = null, $ongoing = null, \Zerebral\BusinessBundle\Model\User\Teacher $teacher = null)
    {
        $this->orderBy('due_at', \Criteria::DESC);
        if ($teacher) {
            $this->filterByTeacher($teacher);
        }
        if ($ongoing === false) {
            $this->add('due_at', null, \Criteria::ISNOTNULL);
        } else if ($ongoing === true) {
            $this->add('due_at', null, \Criteria::ISNULL);
        }
        $this->leftJoinStudentAssignment();
        $this->leftJoin('StudentAssignment.FileReferences FileReference');
        $this->add('student_assignments.id', null, \Criteria::ISNOTNULL);

        if ($course) {
            $this->filterByCourseId($course->getId());
        }

        $this->withColumn('SUM(IF(student_assignments.is_submitted = 1, 1, 0))', 'completedCount');
        $this->withColumn('COUNT(DISTINCT student_assignments.id) - SUM(IF(student_assignments.is_submitted = 1, 1, 0))', 'remainingCount');
        $this->withColumn('COUNT(IF(student_assignments.is_submitted = 1, `FileReference`.file_id , null))', 'filesCount');

        $this->addGroupByColumn('assignments.id');

        return $this;
    }
}
