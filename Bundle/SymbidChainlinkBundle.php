<?php

namespace Symbid\Chainlink\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symbid\Chainlink\Bundle\DependencyInjection\CompilerPass\HandleTagsPass;

class SymbidChainlinkBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new HandleTagsPass());
    }
}
