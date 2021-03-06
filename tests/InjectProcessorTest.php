<?php declare(strict_types=1);

namespace Tests\Incompass\InjectionBundle;

use Incompass\InjectionBundle\Annotation\Factory;
use Incompass\InjectionBundle\Annotation\Inject;
use Incompass\InjectionBundle\Annotation\MethodCall;
use Incompass\InjectionBundle\Annotation\Tag;
use Incompass\InjectionBundle\InjectProcessor;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class InjectProcessorTest
 *
 * @author  Joe Mizzi <themizzi@me.com>
 */
class InjectProcessorTest extends TestCase
{
    private $processor;
    private $container;

    protected function setUp()
    {
        $this->processor = new InjectProcessor();
        $this->container = $this->prophesize(ContainerBuilder::class);
        $this->container->getParameter('kernel.environment')->willReturn('test');
    }

    /** @test */
    public function it_does_not_set_definition_if_not_inject(): void
    {
        $this->container->setDefinition(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process(new \stdClass(), \stdClass::class, $this->container->reveal());
    }

    /** @test */
    public function it_sets_child_definition(): void
    {
        $annotation = new Inject();
        $annotation->parent = 'parent';

        $this->container->setDefinition('class', Argument::type(ChildDefinition::class))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_definition(): void
    {
        $annotation = new Inject();

        $this->container->setDefinition('class', Argument::type(Definition::class))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_aliases(): void
    {
        $annotation = new Inject();
        $annotation->aliases = ['alias1', 'alias2'];

        $this->container->setAlias('alias1', Argument::type(Alias::class))->shouldBeCalled();
        $this->container->setAlias('alias2', Argument::type(Alias::class))->shouldBeCalled();
        $this->container->setDefinition('class', Argument::type(Definition::class))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_decorated_service(): void
    {
        $annotation = new Inject();
        $annotation->decoratedService = 'decoratedService';

        $this->container->setDefinition('class', Argument::that(function (Definition $definition) {
            return 'decoratedService' == $definition->getDecoratedService()[0];
        }))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_class(): void
    {
        $annotation = new Inject();
        $annotation->class = 'someClass';

        $this->container->setDefinition('class', Argument::that(function (Definition $definition) {
            return ('someClass' == $definition->getClass());
        }))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_arguments(): void
    {
        $annotation = new Inject();
        $argument1 = new \Incompass\InjectionBundle\Annotation\Argument();
        $argument1->name = 'arg1';
        $argument1->value = 'val1';
        $argument2 = new \Incompass\InjectionBundle\Annotation\Argument();
        $argument2->name = 'arg2';
        $argument2->value = 'val2';

        $annotation->arguments = [$argument1, $argument2];

        $this->container->setDefinition('class', Argument::that(function (Definition $definition) {
            return empty(array_diff(['$arg1' => 'val1', '$arg2' => 'val2'], $definition->getArguments()));
        }))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_references_as_arguments(): void
    {
        $annotation = new Inject();
        $argument1 = new \Incompass\InjectionBundle\Annotation\Argument();
        $argument1->name = 'arg1';
        $argument1->value = '@val1';
        $argument2 = new \Incompass\InjectionBundle\Annotation\Argument();
        $argument2->name = 'arg2';
        $argument2->value = '@val2';

        $annotation->arguments = [$argument1, $argument2];

        $this->container->setDefinition('class', Argument::that(function (Definition $definition) {
            return empty(array_diff(['$arg1' => new Reference('val1'), '$arg2' => new Reference('val2')], $definition->getArguments()));
        }))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_references_as_arguments_with_concatenated_strings(): void
    {
        $annotation = new Inject();
        $argument1 = new \Incompass\InjectionBundle\Annotation\Argument();
        $argument1->name = 'arg1';
        $argument1->value = 'val1';


        $annotation->arguments = [$argument1];
        $annotation->decoratedService = 'decoratedService';

        $this->container->setDefinition('class', Argument::that(function (Definition $definition) {
            return empty(array_diff(['$arg1' => new Reference('val1.inner')], $definition->getArguments()));
        }))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_method_calls(): void
    {
        $annotation = new Inject();
        $methodCall1 = new MethodCall();
        $methodCall1->method = 'method1';
        $methodCall1->arguments = ['arg1', 'arg2'];
        $methodCall2 = new MethodCall();
        $methodCall2->method = 'method2';
        $methodCall2->arguments = ['arg1', 'arg2'];
        $annotation->methodCalls = [$methodCall1, $methodCall2];

        $this->container->setDefinition('class', Argument::that(function (Definition $definition) {
            $calls = $definition->getMethodCalls();
            return $calls[0][0] === 'method1'
                && $calls[0][1][0] === 'arg1'
                && $calls[0][1][1] === 'arg2'
                && $calls[1][0] === 'method2'
                && $calls[1][1][0] === 'arg1'
                && $calls[1][1][1] === 'arg2';
        }))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_does_not_configure_if_environment_excluded(): void
    {
        $annotation = new Inject();
        $annotation->parent = 'parent';
        $annotation->environments = ['test'];

        $this->container->setDefinition('class', Argument::type(ChildDefinition::class))->shouldNotBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_does_configure_if_environment_included(): void
    {
        $annotation = new Inject();
        $annotation->parent = 'parent';
        $annotation->environments = ['test'];
        $annotation->environmentStrategy = 'include';

        $this->container->setDefinition('class', Argument::type(ChildDefinition::class))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_does_not_configure_if_environment_group_excluded(): void
    {
        $annotation = new Inject();
        $annotation->parent = 'parent';
        $annotation->environments = ['staging'];
        $annotation->environmentGroups = ['test'];
        $annotation->environmentStrategy = 'exclude';

        $this->container->setDefinition('class', Argument::type(ChildDefinition::class))->shouldNotBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn(
            [
                'test' =>
                    [
                        'environments' => ['test']
                    ]
            ]
        );
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_configures_if_environment_group_included(): void
    {
        $annotation = new Inject();
        $annotation->parent = 'parent';
        $annotation->environmentGroups = ['test'];
        $annotation->environmentStrategy = 'include';

        $this->container->setDefinition('class', Argument::type(ChildDefinition::class))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn(
            [
                'test' =>
                    [
                        'environments' => ['test']
                    ]
            ]
        );
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_sets_factory(): void
    {
        $annotation = new Inject();
        $factory = new Factory();
        $factory->class = 'factory';
        $factory->method = 'method';

        $annotation->factory = $factory;

        $this->container->setDefinition('class', Argument::that(function (Definition $definition) {
            return empty(array_diff(['factory', 'method'], $definition->getFactory()));
        }))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }

    /** @test */
    public function it_adds_tag(): void
    {
        $annotation = new Inject();
        $tag = new Tag();
        $tag->name = 'tag';
        $tag->attributes = ['attribute' => 'test'];

        $annotation->tags = [$tag];

        $this->container->setDefinition('class', Argument::that(function (Definition $definition) {
            return empty(array_diff(['attribute' => 'test'], $definition->getTag('tag')[0]));
        }))->shouldBeCalled();

        $this->container->getParameter('injection.environment_groups')->willReturn([]);
        $this->processor->process($annotation, 'class', $this->container->reveal());
    }
}