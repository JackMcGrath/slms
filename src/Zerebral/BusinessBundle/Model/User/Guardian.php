<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseGuardian;

class Guardian extends BaseGuardian
{
    public function getSelectedChild($childId = null) {
        $criteria = null;

        if (!is_null($childId)) {
            $criteria = new \Criteria();
            $criteria->add(StudentPeer::ID, $childId, \Criteria::EQUAL);
        }

        return $this->getStudents($criteria)->getFirst();
    }

    public function isGuardianFor(Student $student)
    {
        return !(is_null($this->getSelectedChild($student->getId())));
    }
}
