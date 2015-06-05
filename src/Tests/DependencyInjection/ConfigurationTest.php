<?php


namespace Symbid\Chainlink\Bundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Symbid\Chainlink\Bundle\DependencyInjection\Configuration;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    /**
     * Return the instance of ConfigurationInterface that should be used by the
     * Configuration-specific assertions in this test-case
     *
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testBlankConfig()
    {
        $this->assertConfigurationIsInvalid(
            [],
            'The child node "contexts" at path "symbid_chainlink" must be configured'
        );
    }

    public function testEmptyContexts()
    {
        $this->assertConfigurationIsInvalid([
                'symbid_chainlink' => [
                    'contexts' => []
                ]
            ], 'The path "symbid_chainlink.contexts" should have at least 1 element(s) defined.'
        );
    }

    public function testContextWithoutTags()
    {
        $this->assertConfigurationIsInvalid([
                'symbid_chainlink' => [
                    'contexts' => [
                        'name' => []
                    ]
                ]
            ], 'The child node "tag" at path "symbid_chainlink.contexts.name" must be configured.'
        );
    }

    public function testValidConfiguration()
    {
        $this->assertConfigurationIsValid([
            'symbid_chainlink' => [
                'contexts' => [
                    'name' => ['tag' => 'this.tag'],
                    'another.name' => ['tag' => 'this.other.tag'],
                ]
            ]
        ]);
    }
}
