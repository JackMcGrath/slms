<?php

namespace Zerebral\CommonBundle\Tests\File\Storage;

use Zerebral\CommonBundle\File\Storage\LocalStorage;
use Symfony\Component\Filesystem\Filesystem;

class LocalStorageTest extends \Zerebral\CommonBundle\Tests\TestCase
{
    /**
     * @var LocalStorage
     */
    private $localStorage = null;

    /**
     * @var FileSystem
     */
    private $fs;

    public function setUp()
    {
        parent::setUp();
        $this->localStorage = new LocalStorage(__DIR__ . '/files', __DIR__ . '/tmp', '/web/files/');
        $this->fs = new Filesystem();
    }

    public function testUpload()
    {
        $file = $this->localStorage->upload(__DIR__ . '/source/test.txt', '', 'hello.jpg');
        $fileInFolder = $this->localStorage->upload(__DIR__ . '/source/test.txt', 'folder', 'hello.jpg');

        $this->assertTrue($this->fs->exists(__DIR__ . '/files/' . $file));
        $this->assertTrue($this->fs->exists(__DIR__ . '/files/' . $fileInFolder));

        $this->fs->remove(__DIR__ . '/files/' . $file);
        $this->fs->remove(__DIR__ . '/files/folder/' . $file);
        $this->fs->remove(__DIR__ . '/files/folder');
        $this->fs->remove(__DIR__ . '/files');
    }

    public function testGetUrl()
    {
        $this->assertEquals('/web/files/some-path/1.png', $this->localStorage->getUrl('some-path/1.png'));
    }

    public function testGetAbsolutePath()
    {
        $this->assertEquals(__DIR__ . '/files/some-path/1.png', $this->localStorage->getAbsolutePath('some-path/1.png'));
    }

    public function testCreateTemporaryFile()
    {
        $this->markTestIncomplete();
    }

    public function testRemoveTemporaryFile()
    {
        $this->markTestIncomplete();
    }

    public function testGetTemporaryFile()
    {
        $this->markTestIncomplete();
    }
}
