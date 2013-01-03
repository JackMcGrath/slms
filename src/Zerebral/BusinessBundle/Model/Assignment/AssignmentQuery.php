<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignmentQuery;

class AssignmentQuery extends BaseAssignmentQuery
{
    public function getCourseAssignmentsDueDate(\Zerebral\BusinessBundle\Model\Course\Course $course = null, $ongoing = null)
    {
        $this->orderBy('due_at', \Criteria::DESC);
        if ($ongoing === false) {
            $this->add('due_at', null, \Criteria::ISNOTNULL);
        } else if ($ongoing === true) {
            $this->add('due_at', null, \Criteria::ISNULL);
        }
        $this->leftJoinStudentAssignment();
        $this->add('student_assignments.id', null, \Criteria::ISNOTNULL);
        $this->addGroupByColumn('assignments.id');
        if ($course) {
            $this->filterByCourseId($course->getId());
        }

        $this->withColumn('SUM(IF(student_assignments.is_submitted = 1, 1, 0))', 'completedCount');
        $this->withColumn('COUNT(student_assignments.id) - SUM(IF(student_assignments.is_submitted = 1, 1, 0))', 'remainingCount');

        return $this;
    }
}
