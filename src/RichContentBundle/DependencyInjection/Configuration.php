<?php

namespace Perform\RichContentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('perform_rich_content');

        $rootNode
            ->children()
                ->arrayNode('block_types')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}
