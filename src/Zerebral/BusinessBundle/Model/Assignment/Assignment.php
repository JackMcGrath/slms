<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignment;

class Assignment extends BaseAssignment
{

    /** @inheritdoc */
    protected function doAddFile($file) {
        $fileReferences = new FileReferences();
        $fileReferences->setFile($file);
        $fileReferences->setreferenceType('assignment');
        $this->addstudentsReferenceName($fileReferences);
    }

    public function preDelete(\PropelPDO $con = null)
    {
        foreach($this->getFileReferencess() as $reference) {
            $reference->delete();
        }
        return parent::preDelete($con);
    }

}
