<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignmentCategoryQuery;
use Zerebral\BusinessBundle\Model\User\Teacher;


class AssignmentCategoryQuery extends BaseAssignmentCategoryQuery
{
    public function findAvailableByTeacher(Teacher $teacher)
    {
        return $this
            ->filterByTeacherId($teacher->getId())
            ->_or()
            ->filterByTeacherId(null)
            ->find();
    }
}
