<?php

namespace Zerebral\BusinessBundle\Tests\Security;

use Zerebral\BusinessBundle\Security\AssignmentAccessVoter;
use Zerebral\BusinessBundle\Model\Assignment\Assignment;
use Zerebral\BusinessBundle\Model\Course\Course;
use Zerebral\BusinessBundle\Model\Course\CourseStudent;
use Zerebral\BusinessBundle\Model\User\Teacher;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\User;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AssignmentAccessVoterTest extends \Zerebral\CommonBundle\Tests\Security\ModelAccessVoterTest
{
    /**
     * @return AssignmentAccessVoter
     */
    protected function mockVoter($isGranted = true, $targetClass = 'MyClass')
    {
        $voter = new AssignmentAccessVoter();
        return $voter;
    }

    protected function mockTargetObject()
    {
        return new Assignment();
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

        $assignment = new Assignment();
        $assignment->setCourse(new Course());
        $assignment->getCourse()->addTeacher($teacher);

        $this->assertNotEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $assignment, array('VIEW','EDIT','DELETE')));
    }

    public function testDeniedForTeacher()
    {
        $teacher1 = $this->mockTeacher(1);
        $token = $this->mockSecurityToken($teacher1->getUser());

        $teacher2 = $this->mockTeacher(2);

        $assignment = new Assignment();
        $assignment->setCourse(new Course());
        $assignment->getCourse()->addTeacher($teacher2);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $assignment, array('VIEW','EDIT','DELETE')));
    }

    public function testGrantedForStudent()
    {
        $teacher = $this->mockTeacher(1);
        $student = $this->mockStudent(1);
        $token = $this->mockSecurityToken($student->getUser());

        $assignment = new Assignment();
        $assignment->setCourse(new Course());
        $assignment->getCourse()->addTeacher($teacher);
        $assignment->getCourse()->addStudent($student);

        $this->assertNotEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $assignment, array('VIEW')));
    }

    public function testDeniedEditForStudent()
    {
        $teacher = $this->mockTeacher(1);
        $student = $this->mockStudent(1);
        $token = $this->mockSecurityToken($student->getUser());

        $assignment = new Assignment();
        $assignment->setCourse(new Course());
        $assignment->getCourse()->addTeacher($teacher);
        $assignment->getCourse()->addStudent($student);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $assignment, array('EDIT')));
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $assignment, array('DELETE')));
    }

    public function testDeniedViewForStudent()
    {
        $teacher = $this->mockTeacher(1);
        $student = $this->mockStudent(1);
        $student2 = $this->mockStudent(2);
        $token = $this->mockSecurityToken($student->getUser());

        $assignment = new Assignment();
        $assignment->setCourse(new Course());
        $assignment->getCourse()->addTeacher($teacher);
        $assignment->getCourse()->addStudent($student2);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->mockVoter()->vote($token, $assignment, array('VIEW')));
    }
}
