<?php


namespace Symbid\Chainlink\Bundle\Tests\DependencyInjection\CompilerPass;

use Mockery as m;
use Mockery\MockInterface;
use Symbid\Chainlink\Bundle\DependencyInjection\CompilerPass\HandleTagsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HandleTagsPassTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContainerBuilder | MockInterface
     */
    protected $container;

    /**
     * @var HandleTagsPass
     */
    protected $pass;

    protected function setUp()
    {
        parent::setUp();

        $this->container = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $this->pass = new HandleTagsPass();
    }

    public function testProcess()
    {
        $contextConfig = [
            'context1' => ['tag' => 'tag1'],
            'context2' => ['tag' => 'tag2'],
        ];

        $taggedServices = [
            'tag1' => ['id1' => [], 'id2' => []],
            'tag2' => ['id3' => []],
        ];

        $this->container
            ->shouldReceive('getParameter')
            ->with('symbid_chainlink.contexts')
            ->once()
            ->andReturn($contextConfig);

        $this->container
            ->shouldReceive('setDefinition')
            ->with(
                '/^symbid_chainlink\.context\..*/',
                m::type('Symfony\Component\DependencyInjection\Definition')
            )
            ->twice();

        $this->container->shouldReceive('setAlias')
            ->with(
                m::anyOf('context1', 'context2'),
                m::anyOf(HandleTagsPass::SERVICE_PREFIX . 'context1', HandleTagsPass::SERVICE_PREFIX . 'context2')
            )
            ->twice();

        $this->container
            ->shouldReceive('hasDefinition')
            ->with(m::anyOf(HandleTagsPass::SERVICE_PREFIX . 'context1', HandleTagsPass::SERVICE_PREFIX . 'context2'))
            ->twice()
            ->andReturn(true);

        $context1Definition = m::mock('Symfony\Component\DependencyInjection\Definition');
        $context1Definition
            ->shouldReceive('addMethodCall')
            ->twice()
            ->with('addHandler', m::type('array'));

        $context2Definition = m::mock('Symfony\Component\DependencyInjection\Definition');
        $context2Definition
            ->shouldReceive('addMethodCall')
            ->once()
            ->with('addHandler', m::type('array'));

        $this->container
            ->shouldReceive('getDefinition')
            ->with(HandleTagsPass::SERVICE_PREFIX . 'context1')
            ->once()
            ->andReturn($context1Definition);

        $this->container
            ->shouldReceive('getDefinition')
            ->with(HandleTagsPass::SERVICE_PREFIX . 'context2')
            ->once()
            ->andReturn($context2Definition);

        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('tag1')
            ->once()
            ->andReturn($taggedServices['tag1']);

        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('tag2')
            ->once()
            ->andReturn($taggedServices['tag2']);

        $this->pass->process($this->container);
    }

    public function testProcessWithoutHandlers()
    {
        $contextConfig = [
            'context1' => ['tag' => 'tag1'],
            'context2' => ['tag' => 'tag2'],
        ];

        $this->container
            ->shouldReceive('getParameter')
            ->with('symbid_chainlink.contexts')
            ->once()
            ->andReturn($contextConfig);

        $this->container
            ->shouldReceive('setDefinition')
            ->with(
                '/^symbid_chainlink\.context\..*/',
                m::type('Symfony\Component\DependencyInjection\Definition')
            )
            ->twice();

        $this->container->shouldReceive('setAlias')
            ->with(
                m::anyOf('context1', 'context2'),
                m::anyOf(HandleTagsPass::SERVICE_PREFIX . 'context1', HandleTagsPass::SERVICE_PREFIX . 'context2')
            )
            ->twice();

        $this->container
            ->shouldReceive('hasDefinition')
            ->with(m::anyOf(HandleTagsPass::SERVICE_PREFIX . 'context1', HandleTagsPass::SERVICE_PREFIX . 'context2'))
            ->twice()
            ->andReturn(true);

        $context1Definition = m::mock('Symfony\Component\DependencyInjection\Definition');
        $context1Definition
            ->shouldReceive('addMethodCall')
            ->never();

        $context2Definition = m::mock('Symfony\Component\DependencyInjection\Definition');
        $context2Definition
            ->shouldReceive('addMethodCall')
            ->never();

        $this->container
            ->shouldReceive('getDefinition')
            ->with(HandleTagsPass::SERVICE_PREFIX . 'context1')
            ->once()
            ->andReturn($context1Definition);

        $this->container
            ->shouldReceive('getDefinition')
            ->with(HandleTagsPass::SERVICE_PREFIX . 'context2')
            ->once()
            ->andReturn($context2Definition);

        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with(m::anyOf('tag1', 'tag2'))
            ->twice()
            ->andReturn([]);

        $this->pass->process($this->container);
    }

    public function testProcessWithoutContexts()
    {
        $contextConfig = [];

        $this->container
            ->shouldReceive('getParameter')
            ->with('symbid_chainlink.contexts')
            ->once()
            ->andReturn($contextConfig);

        $this->container
            ->shouldReceive('setDefinition')
            ->never();

        $this->container
            ->shouldReceive('hasDefinition')
            ->never();

        $this->container
            ->shouldReceive('getDefinition')
            ->never();

        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->never();

        $this->pass->process($this->container);
    }

    public function testProcessWithDefinitionFailure()
    {
        $contextConfig = [
            'context1' => ['tag' => 'tag1'],
            'context2' => ['tag' => 'tag2'],
        ];

        $taggedServices = [
            'tag1' => ['id1' => [], 'id2' => []],
            'tag2' => ['id3' => []],
        ];

        $this->container
            ->shouldReceive('getParameter')
            ->with('symbid_chainlink.contexts')
            ->once()
            ->andReturn($contextConfig);

        $this->container
            ->shouldReceive('setDefinition')
            ->with(
                '/^symbid_chainlink\.context\..*/',
                m::type('Symfony\Component\DependencyInjection\Definition')
            )
            ->twice();

        $this->container->shouldReceive('setAlias')
            ->with(
                m::anyOf('context1', 'context2'),
                m::anyOf(HandleTagsPass::SERVICE_PREFIX . 'context1', HandleTagsPass::SERVICE_PREFIX . 'context2')
            )
            ->twice();

        $this->container
            ->shouldReceive('hasDefinition')
            ->with(m::anyOf(HandleTagsPass::SERVICE_PREFIX . 'context1', HandleTagsPass::SERVICE_PREFIX . 'context2'))
            ->twice()
            ->andReturn(false);

        $this->container
            ->shouldReceive('getDefinition')
            ->never();

        $this->container
            ->shouldReceive('getDefinition')
            ->never();

        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->never();

        $this->pass->process($this->container);
    }
}
