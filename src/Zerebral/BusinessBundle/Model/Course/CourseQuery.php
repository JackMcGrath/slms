<?php

namespace Zerebral\BusinessBundle\Model\Course;

use Zerebral\BusinessBundle\Model\Course\om\BaseCourseQuery;

class CourseQuery extends BaseCourseQuery
{

    /**
     * Find courses by Teacher id
     *
     * @param integer $id
     * @return Course|Course[]|mixed the result, formatted by the current formatter
     */
    public function findByTeacher($id){
       return $this->findByCreatedBy($id);
    }
}
