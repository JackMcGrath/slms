<?php

namespace Zerebral\BusinessBundle\Model\Material;

use Zerebral\BusinessBundle\Model\Material\om\BaseCourseFolderQuery;
use Zerebral\BusinessBundle\Model\Course\Course;

class CourseFolderQuery extends BaseCourseFolderQuery
{
    public function findAvailableByCourse(Course $course)
    {
        return $this
            ->filterByCourseId($course->getId())
            ->find();
    }
}
