<?php

namespace Zerebral\CommonBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ZerebralCommonBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new \Zerebral\CommonBundle\File\DependencyInjection\StorageManagerCompilerPass());
    }
}
