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


    public function getSelectedChildWithSummary($childId = null) {
        $child = $this->getSelectedChild($childId);
        $connection = \Propel::getConnection();

        // ===================== GRADES SUMMARY ======================//

        $query = <<<SQL
        SELECT
            -- `student_assignments`.`grading`, `assignments`.`grade_type`, `assignments`.`threshold`,
            IF(`assignments`.`grade_type` = "pass", `student_assignments`.`grading`, `student_assignments`.`grading` > `assignments`.`threshold`) AS `isPassed`, COUNT(`student_assignments`.`id`) AS `totalCount`
        FROM `student_assignments`
        LEFT JOIN `assignments` ON `student_assignments`.`assignment_id` = `assignments`.`id`
        WHERE
            `student_assignments`.`student_id` = :student_id AND `student_assignments`.`grading` IS NOT NULL AND (`assignments`.`threshold` IS NOT NULL OR `assignments`.`grade_type` = 'pass')
        GROUP BY `isPassed`
SQL;

        $statement = $connection->prepare($query);
        $statement->execute(array(':student_id' => $child->getId()));
        $gradesResult = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $grades = array('totalCount' => 0, 'passed' => 0, 'failed' => 0);
        foreach ($gradesResult as $result) {
            $grades['totalCount'] += $result['totalCount'];
            $type = ($result['isPassed'] == 1) ? 'passed' : 'failed';
            $grades[$type] += $result['totalCount'];
        }
        $grades['passedPercents'] = round($grades['passed'] * 100 / $grades['totalCount']);
        $grades['failedPercents'] = round($grades['failed'] * 100 / $grades['totalCount']);
        $child->setVirtualColumn('grades', $grades);


        // ===================== ATTENDANCE SUMMARY ======================//

        $query = <<<SQL
        SELECT COUNT(`student_attendance`.`attendance_id`) AS `totalCount`, `student_attendance`.`status`
        FROM `student_attendance`
        WHERE `student_attendance`.`student_id` = :student_id
        GROUP BY `student_attendance`.`status`
SQL;

        $statement = $connection->prepare($query);
        $statement->execute(array(':student_id' => $child->getId()));
        $attendanceResult = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $attendance = array('totalCount' => 0, 'present' => 0, 'tardy' => 0, 'absent' => 0, 'excused' => 0);
        foreach ($attendanceResult as $result) {
            $attendance['totalCount'] += $result['totalCount'];
            $type = $result['status'];
            $attendance[$type] += $result['totalCount'];
        }
        $attendance['presentPercents'] = round($attendance['present'] * 100 / $attendance['totalCount']);
        $attendance['tardyPercents'] = round($attendance['tardy'] * 100 / $attendance['totalCount']);
        $attendance['absentPercents'] = round($attendance['absent'] * 100 / $attendance['totalCount']);
        $attendance['excusedPercents'] = round($attendance['excused'] * 100 / $attendance['totalCount']);
        $child->setVirtualColumn('attendance', $attendance);


//        // ===================== CLASSES SUMMARY ======================//
//
//        $query = <<<SQL
//        SELECT COUNT(`student_attendance`.`attendance_id`) AS `totalCount`, `student_attendance`.`status`
//        FROM `student_attendance`
//        WHERE `student_attendance`.`student_id` = :student_id
//        GROUP BY `student_attendance`.`status`
//SQL;
//
//        $statement = $connection->prepare($query);
//        $statement->execute(array(':student_id' => $child->getId()));
//        $attendanceResult = $statement->fetchAll(\PDO::FETCH_ASSOC);
//        $attendance = array('totalCount' => 0, 'present' => 0, 'tardy' => 0, 'absent' => 0, 'excused' => 0);
//        foreach ($attendanceResult as $result) {
//            $attendance['totalCount'] += $result['totalCount'];
//            $type = $result['status'];
//            $attendance[$type] += $result['totalCount'];
//        }
//        $attendance['presentPercents'] = round($attendance['present'] * 100 / $attendance['totalCount']);
//        $attendance['tardyPercents'] = round($attendance['tardy'] * 100 / $attendance['totalCount']);
//        $attendance['absentPercents'] = round($attendance['absent'] * 100 / $attendance['totalCount']);
//        $attendance['excusedPercents'] = round($attendance['excused'] * 100 / $attendance['totalCount']);
//        $child->setVirtualColumn('attendance', $attendance);


        return $child;

    }

    public function isGuardianFor(Student $student)
    {
        return !(is_null($this->getSelectedChild($student->getId())));
    }
}
