<?php

namespace Zerebral\BusinessBundle\Model\Assignment;

use Zerebral\BusinessBundle\Model\Assignment\om\BaseStudentAssignmentPeer;

class StudentAssignmentPeer extends BaseStudentAssignmentPeer
{
    public static function getNextPrev($studentAssignments, $currentStudentAssignment)
    {
        $studentId = $currentStudentAssignment->getStudentId();

        $actualStudents = array();
        /** @var \Zerebral\BusinessBundle\Model\Assignment\StudentAssignment $studentAssignment */
        foreach ($studentAssignments as $studentAssignmentKey => $studentAssignmentActual) {
            if ($studentAssignmentActual->isReadyForGrading()) {
                if ($studentAssignmentActual->getStudentId() == $studentId) {
                    $currentNumber = $studentAssignmentKey;
                }
                $actualStudents[$studentAssignmentKey] = $studentAssignmentActual;
            }
        }
        $totalCount = count($actualStudents);
        $prevStudentId = isset($actualStudents[$currentNumber-1]) ? $actualStudents[$currentNumber-1]->getId() : null;
        $nextStudentId = isset($actualStudents[$currentNumber+1]) ? $actualStudents[$currentNumber+1]->getId() : null;

        $nextPrev = array('currentNumber' => $currentNumber + 1, 'totalCount' => $totalCount, 'prevId' => $prevStudentId, 'nextId' => $nextStudentId);
        return $nextPrev;
    }
}
