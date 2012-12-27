<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseStudentAssignment;

use Zerebral\BusinessBundle\Model\File\FileReferences;

class StudentAssignment extends BaseStudentAssignment
{
    public function preInsert(\PropelPDO $con = null)
    {
        //@todo fix it
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }

    /** @inheritdoc */
    protected function doAddFile($file) {
        $fileReferences = new FileReferences();
        $fileReferences->setFile($file);
        $fileReferences->setreferenceType('studentassignment');
        $this->addFileReferences($fileReferences);
    }
}
