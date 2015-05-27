<?php

namespace Symbid\Chainlink\Bundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Alias;
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
    const SERVICE_PREFIX = 'symbid_chainlink.context.';

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
        $this->container->setDefinition(self::SERVICE_PREFIX . $contextName, $definition);

        $this->container->setAlias($contextName, self::SERVICE_PREFIX . $contextName);
    }

    /**
     * Attaches tagged handlers to the appropriate context
     *
     * @param string $contextName
     * @param string $tag
     */
    protected function attachHandlers($contextName, $tag)
    {
        $serviceName = self::SERVICE_PREFIX . $contextName;

        if ( ! $this->container->hasDefinition($serviceName)) {
            return;
        }

        $definition = $this->container->getDefinition($serviceName);

        foreach ($this->container->findTaggedServiceIds($tag) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (array_key_exists('priority', $attributes)) {
                    $definition->addMethodCall('addHandler', [new Reference($id), $attributes['priority']]);
                } else {
                    $definition->addMethodCall('addHandler', [new Reference($id)]);
                }
            }
        }

    }
}
