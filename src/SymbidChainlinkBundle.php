<?php

namespace DMS\Chainlink\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use DMS\Chainlink\Bundle\DependencyInjection\CompilerPass\HandleTagsPass;

/**
 * Class DMSChainlinkBundle
 *
 * @package DMS\Chainlink\Bundle
 */
class DMSChainlinkBundle extends Bundle
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
