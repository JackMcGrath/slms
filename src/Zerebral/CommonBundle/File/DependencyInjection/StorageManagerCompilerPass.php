<?php

namespace Zerebral\CommonBundle\File\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class StorageManagerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('file_storage.manager')) {
            return;
        }

        $storageManagerDefinition = $container->getDefinition('file_storage.manager');
        $storageServices = $container->findTaggedServiceIds('file_storage');

        foreach ($storageServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $storageManagerDefinition->addMethodCall(
                    'add',
                    array(new Reference($id), $attributes['alias'])
                );

                if (!empty($attributes['default'])) {
                    $storageManagerDefinition->addMethodCall('setDefaultStorageAlias', array($attributes['alias']));
                    $container->setAlias("file_storage", $id);
                }

                $container->setAlias("file_storage." . $attributes['alias'], $id);
            }
        }
    }

} 