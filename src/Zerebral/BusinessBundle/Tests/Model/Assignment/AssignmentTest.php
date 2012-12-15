<?php

namespace Zerebral\BusinessBundle\Tests\Model\User;

use Zerebral\BusinessBundle\Model\Assignment\Assignment;

use Zerebral\BusinessBundle\Model\User\TeacherQuery;
use Zerebral\BusinessBundle\Model\Course\CourseQuery;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentCategoryQuery;

use Zerebral\BusinessBundle\Model\File\File;

class AssignmentTest extends \Zerebral\BusinessBundle\Tests\TestCase {
    public function testAssignmentCreation() {

        $teacherQuery = new TeacherQuery();
        $courseQuery = new CourseQuery();
        $assignmentCategoryQuery = new AssignmentCategoryQuery();

        $assignment = new Assignment();
        $assignment->setName('Test');
        $assignment->setDescription('Descripnio');
        $assignment->setTeacher($teacherQuery->findOne());
        $assignment->setCourse($courseQuery->findOne());
        $assignment->setAssignmentCategory($assignmentCategoryQuery->findOne());
        $assignment->setMaxPoints(10);


        $file = new File();
        $file->setName('test.txt');
        $file->setSize(21038);
        $file->setMimeType('text');
        $file->setCreatedAt(new \DateTime());

        $file2 = new File();
        $file2->setName('test2.txt');
        $file2->setSize(21039);
        $file2->setMimeType('image/png');
        $file2->setCreatedAt(new \DateTime());


        $assignment->addFile($file);
        $assignment->addFile($file2);


        $assignment->save();


        $assignment->delete();

    }
}
