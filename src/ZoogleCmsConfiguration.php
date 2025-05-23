<?php

declare(strict_types=1);

namespace Zantolov\Zoogle\Symfony;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ZoogleCmsConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zoogle_cms');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()->booleanNode('cache')->end(); // @phpstan-ignore-line

        $rootNode->children() // @phpstan-ignore-line
            ->arrayNode('google_api')
            ->children()
            ->scalarNode('client_id')->end()
            ->scalarNode('auth_file')->end()
            ->scalarNode('google_drive_root_directory')->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
