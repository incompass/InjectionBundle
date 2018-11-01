<?php declare(strict_types=1);
/**
 * This file belongs to Bandit. All rights reserved
 */

namespace Incompass\InjectionBundle;

use Incompass\InjectionBundle\Annotation\Argument;
use Incompass\InjectionBundle\Annotation\Inject;
use Incompass\InjectionBundle\Annotation\MethodCall;
use Incompass\InjectionBundle\Annotation\Tag;
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

        $environmentGroups = $container->getParameter('injection.environment_groups');

        if ($annotation->environmentStrategy === 'exclude') {
            if ($annotation->environments) {
                if (\in_array($container->getParameter('kernel.environment'), $annotation->environments, true)) {
                    return;
                }
            }
            if ($environmentGroups) {
                foreach ($annotation->environmentGroups as $group) {
                    if (isset($environmentGroups[$group]['environments']) &&
                        \in_array(
                            $container->getParameter('kernel.environment'),
                            $environmentGroups[$group]['environments'],
                            true)
                    ) {
                        return;
                    }
                }
            }
        } else {
            if ($annotation->environments) {
                if (!\in_array($container->getParameter('kernel.environment'), $annotation->environments, true)) {
                    return;
                }
            }
            if ($environmentGroups) {
                foreach ($annotation->environmentGroups as $group) {
                    if (isset($environmentGroups[$group]['environments']) &&
                        !\in_array(
                            $container->getParameter('kernel.environment'),
                            $environmentGroups[$group]['environments'],
                            true)
                    ) {
                        return;
                    }
                }
            }
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