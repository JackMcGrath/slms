<?php

namespace Zerebral\BusinessBundle\Model\Course;

use Zerebral\BusinessBundle\Model\Course\om\BaseCourseStudent;

class CourseStudent extends BaseCourseStudent
{
    public function preInsert(\PropelPDO $con = null)
    {
        //@todo fix it
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }
}
