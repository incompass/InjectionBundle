<?php declare(strict_types=1);
/**
 * This file belongs to Bandit. All rights reserved
 */

namespace InjectionBundle;

use InjectionBundle\Annotation\Argument;
use InjectionBundle\Annotation\Inject;
use InjectionBundle\Annotation\MethodCall;
use InjectionBundle\Annotation\Tag;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class InjectProcessor
 * @package InjectionBundle
 * @author  Joe Mizzi <themizzi@me.com>
 */
class InjectProcessor
{
    public function process($annotation, $class, ContainerBuilder $container): void
    {
        if (!($annotation instanceof Inject)) {
            return;
        }

        if (\in_array($container->getParameter('kernel.environment'), $annotation->environments, true)) {
            return;
        }

        if ($annotation->parent) {
            $definition = new ChildDefinition($annotation->parent);
        } else {
            $definition = new Definition($class);
        }

        foreach ($annotation->aliases as $alias) {
            $container->setAlias($alias, new Alias($class));
        }

        /** @var Argument $argument */
        foreach ($annotation->arguments as $argument) {
            $definition->setArgument('$'.$argument->name, $argument->value);
        }

        /** @var MethodCall $methodCall */
        foreach ($annotation->methodCalls as $methodCall) {
            $definition->addMethodCall($methodCall->method, $methodCall->arguments);
        }

        /**
         * @var Tag $tag
         */
        foreach ($annotation->tags as $tag) {
            $definition->addTag($tag->name, $tag->attributes);
        }

        if (!$annotation->parent) {
            $definition->setAutoconfigured($annotation->autoconfigured);
        }

        $definition->setAutowired($annotation->autowired);
        $definition->setPublic($annotation->public);
        $definition->setLazy($annotation->lazy);
        $definition->setAbstract($annotation->abstract);
        $definition->setShared($annotation->shared);

        $container->setDefinition($annotation->id ?: $class, $definition);
    }
}