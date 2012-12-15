<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignment;

class Assignment extends BaseAssignment
{
    public function preDelete(\PropelPDO $con = null)
    {
        foreach($this->getFileReferencess() as $reference) {
            $reference->delete();
        }
        return parent::preDelete($con);
    }

}
