<?php

namespace Zerebral\BusinessBundle\Model\Material;

use Zerebral\BusinessBundle\Model\Material\om\BaseCourseMaterial;

class CourseMaterial extends BaseCourseMaterial
{
    public function preInsert(\PropelPDO $con = null)
    {
        //@todo fix it
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }


}
