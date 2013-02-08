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
        $summary = array();

       // var_dump($child->getId());

        // ==================== COURSES AND ATTENDANCE STATS =================//
        $query = <<<SQL
        SELECT courses.name AS courseName, IFNULL(TRIM(BOTH "," FROM GROUP_CONCAT(student_attendance.status)), "") AS attendance, IF(courses.end IS NULL, 0, courses.end > NOW()) AS isPassed
        FROM course_students
        LEFT JOIN courses ON courses.id = course_students.course_id
        LEFT JOIN attendance ON attendance.course_id = courses.id
        LEFT JOIN student_attendance ON student_attendance.attendance_id = attendance.id AND student_attendance.student_id = :student_id
        WHERE course_students.student_id = :student_id
        GROUP BY courses.id
SQL;

        $statement = $connection->prepare($query);
        $statement->execute(array(':student_id' => $child->getId()));
        $attendanceResult = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $summary['coursesCount'] = count($attendanceResult);
        $summary['assignmentsCount'] = \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery::create()->filterByStudent($child)->count();
        $summary['coursesPassedCount'] = count(array_filter($attendanceResult, function($value) {
            return $value['isPassed'] == '1';
        }));
        $summary['coursesPassedPercent'] = round($summary['coursesPassedCount'] * 100 / $summary['coursesCount']);

        $attendanceRecords = array_reduce($attendanceResult, function($array, $value) {
            if (strlen($value['attendance']) > 0) {
                $array = array_merge($array, explode(',', $value['attendance']));
            }
            return $array;
        }, array());
        $attendance = array('present' => 0, 'tardy' => 0, 'absent' => 0, 'excused' => 0);
        foreach ($attendanceRecords as $record) {
            $attendance[$record]++;
        }
        $attendanceCount = count($attendanceRecords);
        $attendance = array_merge($attendance, array(
            'presentPercent' => round($attendance['present'] * 100 / $attendanceCount),
            'tardyPercent' => round($attendance['tardy'] * 100 / $attendanceCount),
            'absentPercent' => round($attendance['absent'] * 100 / $attendanceCount),
            'excusedPercent' => round($attendance['excused'] * 100 / $attendanceCount)
        ));
        $attendance['totalCount'] = $attendanceCount;
        $summary['attendance'] = $attendance;




        $query = <<<SQL
        SELECT courses.name AS courseName, GROUP_CONCAT(IF(assignments.grade_type = "pass", student_assignments.grading = 1,  student_assignments.grading  >= IFNULL(assignments.threshold, 99999))) AS isPassed
        FROM student_assignments
        LEFT JOIN assignments ON assignments.id = student_assignments.assignment_id
        LEFT JOIN courses ON courses.id = assignments.course_id
        WHERE student_assignments.student_id = :student_id AND student_assignments.grading IS NOT NULL
        GROUP BY courses.id
SQL;

        $statement = $connection->prepare($query);
        $statement->execute(array(':student_id' => $child->getId()));
        $gradesResult = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $summary = array_merge($summary, array('gradesCount' => 0, 'gradesPassedCount' => 0, 'gradesPassedPercent' => 0));

        foreach ($gradesResult as $result) {
            $grades = explode(',', $result['isPassed']);
            $summary['gradesCount'] += count($grades);
            $summary['gradesPassedCount'] += count(array_filter($grades, function($value) {
                return $value == '1';
            }));
        }
        $summary['gradesPassedPercent'] = round($summary['gradesPassedCount'] * 100 / $summary['gradesCount']);


        $classes = array();
        foreach ($gradesResult as $result) {
            if (!isset($classes[$result['courseName']])) {
                $classes[$result['courseName']] = array('gradesCount' => 0, 'gradesPassedCount' => 0, 'gradesPassedPercent' => 0, 'attendanceCount' => 0, 'attendancePresentCount' => 0, 'attendancePresentPercent' => 0);
            }

            $grades = explode(',', $result['isPassed']);
            $gradesPassedCount = count(array_filter($grades, function($value) {
                return $value == '1';
            }));
            $classes[$result['courseName']]['gradesCount'] = count($grades);
            $classes[$result['courseName']]['gradesPassedCount'] = $gradesPassedCount;
            $classes[$result['courseName']]['gradesPassedPercent'] = round($gradesPassedCount * 100 / count($grades));
        }

        foreach ($attendanceResult as $result) {
            if (!isset($classes[$result['courseName']])) {
                $classes[$result['courseName']] = array('gradesCount' => 0, 'gradesPassedCount' => 0, 'gradesPassedPercent' => 0, 'attendanceCount' => 0, 'attendancePresentCount' => 0, 'attendancePresentPercent' => 0);
            }

            if (strlen($result['attendance']) > 0) {
                $attendance = explode(',', $result['attendance']);
                $classes[$result['courseName']]['attendanceCount'] = count($attendance);
                $classes[$result['courseName']]['attendancePresentCount'] = count(array_filter($attendance, function($value) {
                    return $value == 'present';
                }));
                $classes[$result['courseName']]['attendancePresentPercent'] = round($classes[$result['courseName']]['attendancePresentCount'] * 100 / count($attendance));
            }
        }


        $summary['classes'] = $classes;
        $child->setVirtualColumn('summary', $summary);


        return $child;

    }

    public function isGuardianFor(Student $student)
    {
        return !(is_null($this->getSelectedChild($student->getId())));
    }
}
