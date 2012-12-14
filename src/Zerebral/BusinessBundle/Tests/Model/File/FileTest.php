<?php

namespace Zerebral\BusinessBundle\Tests\Model\User;

use Zerebral\BusinessBundle\Model\File\File;

use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;

class FileTest extends \Zerebral\BusinessBundle\Tests\TestCase {
    public function testFileCreation() {
        $file = new File();

        $file->setName('test.txt');
        $file->setSize(21038);
        $file->setMimeType('text');
        $file->setCreatedAt(new \DateTime());



        $assignmentQuery = new AssignmentQuery();
        $assignment = $assignmentQuery->findOne();

        $file->addAssignment($assignment);

        $file->save();


        $file->delete();
    }
}
