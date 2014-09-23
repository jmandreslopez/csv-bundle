<?php

namespace Sample\SecurityBundle;

use Sample\SecurityBundle\Security\Factory\ADFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SampleSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ADFactory());

    }
}
