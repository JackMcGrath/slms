<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseUserQuery;

class UserQuery extends BaseUserQuery
{
    public function getRelatedUsers(User $user)
    {
        //$criteria = new \Criteria();

        $usersToCoursesJoin = new \Join();
        $usersToCoursesJoin->setJoinType(\Criteria::LEFT_JOIN);
        if ($user->isStudent()) {
            $usersToCoursesJoin->addExplicitCondition(null, $user->getStudent()->getId(), null, 'course_students', 'student_id', 'userToCourses');
        } else {
            $usersToCoursesJoin->addExplicitCondition(null, $user->getTeacher()->getId(), null, 'course_teachers', 'teacher_id', 'userToCourses');
        }
        $this->addJoinObject($usersToCoursesJoin);

        //LEFT JOIN course_students AS courseToStudents ON studentToCourses.course_id = courseToStudents.course_id
        $courseToStudentsJoin = new \Join();
        $courseToStudentsJoin->setJoinType(\Criteria::LEFT_JOIN);
        $courseToStudentsJoin->addExplicitCondition('userToCourses', 'course_id', null, 'course_students', 'course_id', 'courseToStudents');
        $this->addJoinObject($courseToStudentsJoin);
        // LEFT JOIN course_teachers AS courseToTeachers ON studentToCourses.course_id = courseToTeachers.course_id
        $courseToTeachersJoin = new \Join();
        $courseToTeachersJoin->setJoinType(\Criteria::LEFT_JOIN);
        $courseToTeachersJoin->addExplicitCondition('userToCourses', 'course_id', null, 'course_teachers', 'course_id', 'courseToTeachers');
        $this->addJoinObject($courseToTeachersJoin);

        // LEFT JOIN students ON courseToStudents.student_id = students.id
        $studentsJoin = new \Join();
        $studentsJoin->setJoinType(\Criteria::LEFT_JOIN);
        $studentsJoin->addExplicitCondition('courseToStudents', 'student_id', null, 'students', 'id', null);
        $this->addJoinObject($studentsJoin);
        // LEFT JOIN teachers ON courseToTeachers.teacher_id = teachers.id
        $teachersJoin = new \Join();
        $teachersJoin->setJoinType(\Criteria::LEFT_JOIN);
        $teachersJoin->addExplicitCondition('courseToTeachers', 'teacher_id', null, 'teachers', 'id', null);
        $this->addJoinObject($teachersJoin);

        $this->where('users.id = students.user_id OR users.id = teachers.user_id');
        $this->groupBy('users.id');

        return $this;
    }
}
