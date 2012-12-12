<?php

namespace Zerebral\BusinessBundle\Tests\Model\User;

use Zerebral\BusinessBundle\Model\User\User;
use Zerebral\BusinessBundle\Model\User\Student;
use Zerebral\BusinessBundle\Model\User\Teacher;

class UserTest extends \Zerebral\BusinessBundle\Tests\TestCase
{
    public function testGetTeacher()
    {
        $user = new User();
        $this->assertNull($user->getTeacher());

        $user->addTeacher(new Teacher());
        $this->assertNotNull($user->getTeacher());
    }

    public function testGetStudent()
    {
        $user = new User();
        $this->assertNull($user->getTeacher());

        $user->addStudent(new Student());
        $this->assertNotNull($user->getStudent());
    }

    public function testTransitToRoleModel()
    {
        $this->markTestIncomplete();
    }

    public function testGetRoleModel()
    {
        $this->markTestIncomplete();
    }

    public function testGetFullName()
    {
        function mockUser($first, $last, $salutation = null) {
            $user = new User();
            $user->setFirstName($first);
            $user->setLastName($last);
            $user->setSalutation($salutation);
            return $user;
        }

        $this->assertEquals('Chris Wonder', mockUser('Chris', 'Wonder')->getFullName());
        $this->assertEquals('Mr. Johns', mockUser('Chris', 'Johns', 'Mr.')->getFullName());
    }
}
