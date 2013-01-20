<?php

namespace Zerebral\CommonBundle\Tests\File\EventHandler;

use Zerebral\BusinessBundle\Model\File\File;
use Zerebral\CommonBundle\File\Storage\Storage;

class FileStorageAwareModelEventHandlerTest extends \Zerebral\CommonBundle\Tests\TestCase
{
    public function testDefaultFileStorageIsSetAfterConstruct()
    {
        /** @var $defaultFileStorage Storage */
        $defaultFileStorage = $this->getContainer()->get('file_storage');

        $file = new File();
        $this->assertEquals($file->getStorage(), $defaultFileStorage->getAlias());
        $this->assertEquals($file->getFileStorage()->getAlias(), $defaultFileStorage->getAlias());
    }

    public function testFileStorageChangedAfterStorageSet()
    {
        $file = new File();

        $file->setStorage('local');
        $this->assertEquals('local', $file->getFileStorage()->getAlias());

        $file->setStorage('s3');
        $this->assertEquals('s3', $file->getFileStorage()->getAlias());
    }

    public function testStorageChangedAfterFileStorageSet()
    {
        $file = new File();

        $file->setFileStorage($this->getContainer()->get('file_storage.local'));
        $this->assertEquals('local', $file->getStorage());

        $file->setFileStorage($this->getContainer()->get('file_storage.s3'));
        $this->assertEquals('s3', $file->getStorage());
    }
}
