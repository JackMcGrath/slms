<?php

namespace Zerebral\CommonBundle\Tests\File\Storage;

use Zerebral\CommonBundle\File\Storage\StorageManager;
use Zerebral\CommonBundle\File\Storage\LocalStorage;

class StorageManagerTest extends \Zerebral\CommonBundle\Tests\TestCase
{
    public function testManageStorages()
    {
        $manager = new StorageManager();

        $manager->add(new LocalStorage('local_folder1'), 'local1');
        $manager->add(new LocalStorage('local_folder2'), 'local2');

        $this->assertTrue($manager->has('local1'));
        $this->assertTrue($manager->has('local2'));
        $this->assertFalse($manager->has('local3'));

        $this->assertEquals(2, $manager->count());

        $this->assertEquals('local_folder1', $manager->get('local1')->getFilesFolder());
        $this->assertEquals('local_folder2', $manager->get('local2')->getFilesFolder());
    }

    public function testAssignAliases()
    {
        $manager = new StorageManager();

        $local1 = new LocalStorage('local_folder1');
        $manager->add($local1, 'local1');

        $local2 = new LocalStorage('local_folder2');
        $manager->add($local2, 'local2');

        $this->assertEquals('local1', $local1->getAlias());
        $this->assertEquals('local2', $local2->getAlias());

        $this->assertEquals('local1', $manager->get('local1')->getAlias());
        $this->assertEquals('local2', $manager->get('local2')->getAlias());
    }

    public function testDefaultStorage()
    {
        $manager = new StorageManager();

        $manager->add(new LocalStorage('local_folder1'), 'local1');
        $manager->add(new LocalStorage('local_folder2'), 'local2');

        $manager->setDefaultStorageAlias('local2');

        $this->assertEquals('local2', $manager->getDefaultStorageAlias());
        $this->assertEquals('local2', $manager->getDefault()->getAlias());
    }
}
