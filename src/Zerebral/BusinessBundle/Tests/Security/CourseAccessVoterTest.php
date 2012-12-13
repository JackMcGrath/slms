<?php

namespace Zerebral\BusinessBundle\Tests\Security;

use Zerebral\BusinessBundle\Security\CourseAccessVoter;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CourseStudent;
use Zerebral\BusinessBundle\Model\User\Teacher;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\User;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CourseAccessVoterTest extends \Zerebral\CommonBundle\Tests\Security\ModelAccessVoterTest
{
    /**
     * @return CourseAccessVoter
     */
    protected function mockVoter($isGranted = true, $targetClass = 'MyClass')
    {
        $voter = new CourseAccessVoter();
        return $voter;
    }

    protected function mockTargetObject()
    {
        return new Course();
    }

    /**
     * @param int $id
     * @return Teacher
     */
    protected function mockTeacher($id = 1)
    {
        $user = new User();
        $user->setRole(User::ROLE_TEACHER);
        $teacher = $user->transitToRoleModel();
        $teacher->setId($id);

        return $teacher;
    }

    /**
     * @param int $id
     * @return Student
     */
    protected function mockStudent($id = 1)
    {
        $user = new User();
        $user->setRole(User::ROLE_STUDENT);
        $student = $user->transitToRoleModel();
        $student->setId($id);

        return $student;
    }

    public function testIsGranted()
    {
        $this->markTestSkipped('Not valid for assignment voter');
    }

    public function testIsGrantedCalled()
    {
        $this->markTestSkipped('Not valid for assignment voter');
    }

    public function testGrantedForTeacher()
    {
        $teacher = $this->mockTeacher(1);
        $token = $this->mockSecurityToken($teacher->getUser());

        $course = new Course();
        $course->addTeacher($teacher);

        $this->assertNotEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $course, array('VIEW','EDIT','DELETE')));
    }

    public function testDeniedForTeacher()
    {
        $teacher1 = $this->mockTeacher(1);
        $token = $this->mockSecurityToken($teacher1->getUser());

        $teacher2 = $this->mockTeacher(2);

        $course = new Course();
        $course->addTeacher($teacher2);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $course, array('VIEW','EDIT','DELETE')));
    }

    public function testGrantedForStudent()
    {
        $teacher = $this->mockTeacher(1);
        $student = $this->mockStudent(1);
        $token = $this->mockSecurityToken($student->getUser());

        $course = new Course();
        $course->addTeacher($teacher);
        $course->addStudent($student);

        $this->assertNotEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $course, array('VIEW')));
    }

    public function testDeniedEditForStudent()
    {
        $teacher = $this->mockTeacher(1);
        $student = $this->mockStudent(1);
        $token = $this->mockSecurityToken($student->getUser());

        $course = new Course();
        $course->addTeacher($teacher);
        $course->addStudent($student);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $course, array('EDIT')));
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $course, array('DELETE')));
    }

    public function testDeniedViewForStudent()
    {
        $teacher = $this->mockTeacher(1);
        $student = $this->mockStudent(1);
        $student2 = $this->mockStudent(2);
        $token = $this->mockSecurityToken($student->getUser());

        $course = new Course();
        $course->addTeacher($teacher);
        $course->addStudent($student2);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $course, array('VIEW')));
    }
}
