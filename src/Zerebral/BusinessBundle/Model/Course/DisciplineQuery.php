<?php

namespace Zerebral\BusinessBundle\Model\Course;

use Zerebral\BusinessBundle\Model\Course\om\BaseDisciplineQuery;

use Zerebral\BusinessBundle\Model\User\Teacher;

class DisciplineQuery extends BaseDisciplineQuery
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
