<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $finder = new ExecutableFinder();

        $rootNode = $treeBuilder->root('sp_bower');
        $rootNode
            ->children()
                ->scalarNode('bin')->defaultValue(function() use ($finder) { return $finder->find('bower', '/usr/bin/bower'); })->end()
                ->booleanNode('register_assets')->defaultValue(true)->end()
                ->arrayNode('paths')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function($v) { return array('config_dir' => $v); })
                        ->end()
                        ->children()
                            ->scalarNode('config_dir')->defaultValue('Resources/config/bower')->end()
                            ->scalarNode('asset_dir')->defaultValue('../../public/components')->end()
                            ->scalarNode('json_file')->defaultValue('component.json')->end()
                            ->scalarNode('endpoint')->defaultValue('https://bower.herokuapp.com')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
