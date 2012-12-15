<?php
namespace Zerebral\CommonBundle\Tests\Component\FileStorage;

use Zerebral\CommonBundle\Component\FileStorage\AbstractFileStorage;

class LocalFileStorageTest extends \Zerebral\BusinessBundle\Tests\TestCase {

    public function testGetFileStorage() {
        $localFileStorage = AbstractFileStorage::getFileStorage('local');
        $this->assertInstanceOf('\Zerebral\CommonBundle\Component\FileStorage\LocalFileStorage', $localFileStorage);

        $this->setExpectedException('\Zerebral\CommonBundle\Component\FileStorage\FileStorageException');
        AbstractFileStorage::getFileStorage('unknown');
    }

    public function testLocalFileStorageSaveMethod() {
        $localFileStorage = AbstractFileStorage::getFileStorage('local');

        $testFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'test.txt';
        $newFileName = 'MyNewFile.txt';
        $this->assertTrue($localFileStorage->save($testFilePath, $newFileName));
        $this->assertTrue(file_exists($localFileStorage->getPath() . DIRECTORY_SEPARATOR . $newFileName));

        $testFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'test2.txt';
        $newFileName = 'MyNewFile2.txt';
        $this->setExpectedException('\Zerebral\CommonBundle\Component\FileStorage\FileStorageException');
        $localFileStorage->save($testFilePath, $newFileName);


    }
}