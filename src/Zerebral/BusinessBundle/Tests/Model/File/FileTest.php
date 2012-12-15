<?php

namespace Zerebral\BusinessBundle\Tests\Model\User;

use Zerebral\BusinessBundle\Model\File\File;

use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;

class FileTest extends \Zerebral\BusinessBundle\Tests\TestCase {
    public function testFileCreation() {
        $file = new File();

        $file->setSourcePath('/home/deespater/Sandbox/uploadFile/File.php');
        //$file->setFileStorage(\Zerebral\CommonBundle\Component\FileStorage\AbstractFileStorage::getFileStorage('local'));


//        $assignmentQuery = new AssignmentQuery();
//        $assignment = $assignmentQuery->findOne();
//        $file->addAssignment($assignment);

        $file->save();

        //$file->delete();
    }
}
