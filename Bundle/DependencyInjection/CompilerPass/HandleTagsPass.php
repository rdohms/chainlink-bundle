<?php

namespace Symbid\Chainlink\Bundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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

        $contexts = $container->getParameter('symbid_chainlink.contexts');

        foreach ($contexts as $context => $config) {
            $this->defineContext($context);
            $this->attachHandlers($context, $config['tag']);
        }
    }

    /**
     * Creates a new Context definition
     *
     * @param string $context
     */
    protected function defineContext($context)
    {
        $definition = new Definition(Context::class);
        $this->container->setDefinition('symbid_chainlink.context.' . $context, $definition);
    }

    /**
     * Attaches tagged handlers to the appropriate context
     *
     * @param string $context
     * @param string $tag
     */
    protected function attachHandlers($context, $tag)
    {
        if (! $this->container->hasDefinition($context)) {
            return;
        }

        $definition = $this->container->getDefinition($context);

        $taggedServices = $this->container->findTaggedServiceIds($tag);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
