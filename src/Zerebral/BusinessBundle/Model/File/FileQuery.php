<?php

namespace Zerebral\BusinessBundle\Model\File;

use Zerebral\BusinessBundle\Model\File\om\BaseFileQuery;

use \Criteria;

class FileQuery extends BaseFileQuery
{
    protected function getReferenceType($object) {
        $type = array_reverse(explode('\\', get_class($object)));
        return strtolower($type[0]);
    }

    /** @inheritdoc */
    public function filterByassignmentReferenceId($assignment, $comparison = Criteria::EQUAL) {
        return $this
            ->useFileReferencesQuery()
            ->filterByassignmentReferenceId($assignment, $comparison)
            ->addAnd('file_references.reference_type', $this->getReferenceType($assignment))
            ->endUse();
    }
}
