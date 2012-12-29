<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseAssignment;
use \Zerebral\BusinessBundle\Model\File\FileReferences;

class Assignment extends BaseAssignment
{

    public function __construct()
    {
        $this->setMaxPoints(100);
    }

    /** @inheritdoc */
    protected function doAddFile($file) {
        $fileReferences = new FileReferences();
        $fileReferences->setFile($file);
        $fileReferences->setreferenceType('assignment');
        $this->addFileReferences($fileReferences);
    }

    public function preDelete(\PropelPDO $con = null)
    {
        foreach($this->getFileReferencess() as $reference) {
            $reference->delete();
        }
        return parent::preDelete($con);
    }

    public function getFeedItem() {
        return $this->getFeedItems()->getFirst();
    }

}
