<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseStudentAssignment;


class StudentAssignment extends BaseStudentAssignment
{
    public function preInsert(\PropelPDO $con = null)
    {
        //@todo fix it
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }

    public function isReadyForGrading()
    {
        return $this->getAssignment()->getDueAt('Y-m-d H:i:s') < date('Y-m-d H:i:s');
    }
}
