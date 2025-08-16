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

        return $treeBuilder;
    }
}
