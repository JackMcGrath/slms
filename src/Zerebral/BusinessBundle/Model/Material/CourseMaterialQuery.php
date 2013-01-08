<?php

namespace Zerebral\BusinessBundle\Model\Material;

use Zerebral\BusinessBundle\Model\Material\om\BaseCourseMaterialQuery;

class CourseMaterialQuery extends BaseCourseMaterialQuery
{
    public function findRecentCourseMaterials($course, $limit = 5)
    {
        $this->setLimit($limit);
        $this->addDescendingOrderByColumn('created_at');
        $this->filterByCourseId($course->getId());
        return $this;
    }
}
