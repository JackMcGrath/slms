<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseUserQuery;

class UserQuery extends BaseUserQuery
{
    public function findForSuggestByNameForUser($name, User $user)
    {
        $request = explode(' ', $name);
        foreach ($request as $word) {
            $this->addAnd('LCASE(first_name)', '%' . strtolower(trim($word)) . '%', \Criteria::LIKE);
            $this->addOr('LCASE(last_name)', '%' . trim($word) . '%', \Criteria::LIKE);
        }
        $this->_and();
        $this->filterById($user->getId(), \Criteria::NOT_EQUAL);

        $this->limit(10);

        return $this->getRelatedUsers($user)->find();
    }

    public function getRelatedUsers(User $user, $excludeMe = false)
    {
        //$criteria = new \Criteria();

        $usersToCoursesJoin = new \Join();
        $usersToCoursesJoin->setJoinType(\Criteria::LEFT_JOIN);

        if ($user->isStudent()) {
            $usersToCoursesJoin->addExplicitCondition(null, $user->getStudent()->getId(), null, 'course_students', 'student_id', 'userToCourses');
        } else if ($user->isTeacher()) {
            $usersToCoursesJoin->addExplicitCondition(null, $user->getTeacher()->getId(), null, 'course_teachers', 'teacher_id', 'userToCourses');
        } else if ($user->isGuardian()) {
            $children = $user->getGuardian()->getStudents();
            $childIds = array();
            foreach($children as $child) {
                $childIds[] = $child->getId();
            }
            $usersToCoursesJoin->addExplicitCondition(null, $user->getGuardianSelectedUser()->getId(), null, 'course_students', 'student_id', 'userToCourses');
            $c = new \Criteria();
            $c->add(null, 'userToCourses.student_id IN (' . implode(', ', $childIds) . ')', \Criteria::CUSTOM);
            $usersToCoursesJoin->setJoinCondition($c->getLastCriterion());
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

        if ($user->isGuardian() || $user->isTeacher()) {
            $studentToGuardiansJon = new \Join();
            $studentToGuardiansJon->setJoinType(\Criteria::LEFT_JOIN);
            $studentToGuardiansJon->addExplicitCondition('courseToStudents', 'student_id', null, 'student_guardians', 'student_id', 'studentToGuardians');
            $this->addJoinObject($studentToGuardiansJon);

            $guardiansJoin = new \Join();
            $guardiansJoin->setJoinType(\Criteria::LEFT_JOIN);
            $guardiansJoin->addExplicitCondition('studentToGuardians', 'guardian_id', null, 'guardians', 'id', null);
            $this->addJoinObject($guardiansJoin);

            $this->_or()->where('users.id = guardians.user_id');
        }

        if ($excludeMe) {
            $this->where('users.id <> ' . $user->getId());
            if ($user->isGuardian()) {
                $this->_and()->where('users.id NOT IN (' . $user->getGuardianSelectedUser()->getUser()->getId() . ')');
            }
        }

        $this->groupBy('users.id');

//        echo $this->toString();
//        die;

        return $this;
    }
}
