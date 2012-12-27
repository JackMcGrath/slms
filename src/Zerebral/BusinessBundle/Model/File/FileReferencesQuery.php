<?php

namespace Zerebral\BusinessBundle\Model\File;

use Zerebral\BusinessBundle\Model\File\om\BaseFileReferencesQuery;
use \Criteria;

class FileReferencesQuery extends BaseFileReferencesQuery
{

    //@todo: Find a way to get REFERENCE_TYPE
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(FileReferencesPeer::FILE_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(FileReferencesPeer::REFERENCE_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $cton2 = $this->getNewCriterion(FileReferencesPeer::REFERENCE_TYPE, 'assignment', Criteria::EQUAL);
            $cton0->addAnd($cton2);
            $this->addOr($cton0);
        }

        return $this;
    }
}
