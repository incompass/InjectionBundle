<?php declare(strict_types=1);

namespace Incompass\InjectionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @author  Joe Mizzi <themizzi@me.com>
 *
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('injection');
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('paths')
                    ->defaultValue(['src' => 'App\\'])
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('environment_groups')
                    ->useAttributeAsKey('group')
                    ->defaultValue([])
                    ->arrayPrototype()
                    ->children()
                        ->arrayNode('environments')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}