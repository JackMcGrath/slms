<?php

namespace Zerebral\CommonBundle\Tests\File\DependencyInjection;

class StorageManagerCompilerPassTest extends \Zerebral\CommonBundle\Tests\TestCase
{
    public function testDefaultStorage()
    {
        $container = $this->getContainer();
        $this->assertInstanceOf('Zerebral\CommonBundle\File\Storage\LocalStorage', $container->get('file_storage'));
    }

    public function testStorageAliases()
    {
        $container = $this->getContainer();

        $this->assertTrue($container->has('file_storage.s3'));
        $this->assertInstanceOf('Zerebral\CommonBundle\File\Storage\LocalStorage', $container->get('file_storage.s3'));

        $this->assertTrue($container->has('file_storage.local'));
        $this->assertInstanceOf('Zerebral\CommonBundle\File\Storage\LocalStorage', $container->get('file_storage.local'));
    }
}
