<?php

namespace Symbid\Chainlink\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symbid\Chainlink\Bundle\DependencyInjection\CompilerPass\HandleTagsPass;

/**
 * Class SymbidChainlinkBundle
 *
 * @package Symbid\Chainlink\Bundle
 */
class SymbidChainlinkBundle extends Bundle
{
    /**
     * Will register our custom pass
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new HandleTagsPass());
    }
}
