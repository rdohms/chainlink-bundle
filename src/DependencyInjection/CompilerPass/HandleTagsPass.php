<?php

namespace Symbid\Chainlink\Bundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class HandleTagsPass
 * Registers configured contexts as services and attaches handlers tagged with the configured tag
 *
 * @package Symbid\Chainlink\Bundle\DependencyInjection\CompilerPass
 */
class HandleTagsPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        $contextNames = $container->getParameter('symbid_chainlink.contexts');

        foreach ($contextNames as $contextName => $config) {
            $this->defineContext($contextName);
            $this->attachHandlers($contextName, $config['tag']);
        }
    }

    /**
     * Creates a new Context definition
     *
     * @param string $contextName
     */
    protected function defineContext($contextName)
    {
        $definition = new Definition('Symbid\Chainlink\Context');
        $this->container->setDefinition('symbid_chainlink.context.' . $contextName, $definition);
    }

    /**
     * Attaches tagged handlers to the appropriate context
     *
     * @param string $contextName
     * @param string $tag
     */
    protected function attachHandlers($contextName, $tag)
    {
        if (! $this->container->hasDefinition($contextName)) {
            return;
        }

        $definition = $this->container->getDefinition($contextName);

        $taggedServices = $this->container->findTaggedServiceIds($tag);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
