<?php

namespace Sample\SecurityBundle\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class ADFactory extends FormLoginFactory
{
    public function __construct()
    {
        parent::__construct();
        $this->addOption('area_parameter', 'area');
    }

    public function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {

        $provider = 'ad.security.authentication.provider.' . $id;
        $container->setDefinition($provider, new DefinitionDecorator('ad.security.authentication.provider'))
            ->replaceArgument(1, new Reference($userProviderId))
            ->replaceArgument(3, $id);
        return $provider;
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'active_login';
    }

    protected function getListenerId()
    {
        return 'ad.security.authentication.listener';
    }

}