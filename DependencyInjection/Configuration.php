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
    const DEFAULT_CONFIG_DIR = 'Resources/config/bower';
    const DEFAULT_CACHE_DIRECTORY = '../../public/components/cache';
    const DEFAULT_CACHE_ID = null;
    const DEFAULT_ASSERT_DIR = '../../public/components';
    const DEFAULT_JSON_FILE = 'bower.json';
    const DEFAULT_ENDPOINT = 'https://bower.herokuapp.com';

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
                ->booleanNode('offline')
                    ->defaultValue(false)
                    ->info('Deprecated since 0.12, to be removed in 0.13. Use options: {offline: true} instead')
                ->end()
                ->booleanNode('allow_root')
                    ->defaultFalse()
                    ->info('Deprecated since 0.12, to be removed in 0.13. Use options: {allow_root: true} instead')
                ->end()
                ->arrayNode('options')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('cache_dir')->cannotBeEmpty()
                    ->defaultValue('%kernel.cache_dir%/sp_bower')
                    ->info('Deprecated since 0.12, to be removed in 0.13')
                ->end()
                ->booleanNode('install_on_warmup')->defaultFalse()->end()
                ->booleanNode('keep_bowerrc')
                    ->defaultTrue()
                    ->info('Deprecated since 0.12, to be removed in 0.13')
                ->end()
                ->arrayNode('assetic')
                    ->addDefaultsIfNotSet()
                    ->treatNullLike(array('enabled' => true))
                    ->treatTrueLike(array('enabled' => true))
                    ->treatFalseLike(array('enabled' => false))
                    ->children()
                        ->booleanNode('enabled')->defaultValue(true)->end()
                        ->arrayNode('nest_dependencies')
                            ->treatNullLike(array('all' => true))
                            ->treatTrueLike(array('all' => true))
                            ->treatFalseLike(array('all' => false))
                            ->prototype('scalar')->end()
                            ->defaultvalue(array('all' => true))
                            ->validate()
                                ->ifTrue(function ($v) { return !isset($v['all']); })
                                ->then(function ($v) {
                                    $v['all'] = true;

                                    return $v;
                                })
                            ->end()
                        ->end()
                        ->arrayNode('filters')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('css')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('js')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('packages')
                                    ->useAttributeAsKey('name')
                                    ->prototype('array')
                                        ->children()
                                            ->arrayNode('css')
                                                ->prototype('scalar')->end()
                                            ->end()
                                            ->arrayNode('js')
                                                ->prototype('scalar')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('bundles')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function($v) { return array('config_dir' => $v); })
                        ->end()
                        ->children()
                            ->scalarNode('config_dir')->defaultValue(self::DEFAULT_CONFIG_DIR)->end()
                            ->arrayNode('cache')
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function($v) { return array('directory' => $v); })
                                ->end()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('directory')->defaultValue(self::DEFAULT_CACHE_DIRECTORY)->end()
                                    ->scalarNode('id')->defaultValue(self::DEFAULT_CACHE_ID)->end()
                                ->end()
                            ->end()
                            ->scalarNode('asset_dir')->defaultValue(self::DEFAULT_ASSERT_DIR)->end()
                            ->scalarNode('json_file')->defaultValue(self::DEFAULT_JSON_FILE)->end()
                            ->scalarNode('endpoint')->defaultValue(self::DEFAULT_ENDPOINT)->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
