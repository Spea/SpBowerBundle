<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Bower;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Process\ProcessBuilder;
use Doctrine\Common\Cache\Cache;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class Bower
{
    /**
     * @var string
     */
    protected $bowerPath;

    /**
     * @var \Symfony\Component\Process\ProcessBuilder
     */
    protected $processBuilder;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $dependencyCache;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param string                                             $bowerPath
     * @param \Doctrine\Common\Cache\Cache                       $dependencyCache
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     */
    public function __construct($bowerPath = '/usr/bin/bower', Cache $dependencyCache, EventDispatcher $eventDispatcher)
    {
        $this->bowerPath = $bowerPath;
        $this->dependencyCache = $dependencyCache;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Installs bower dependencies from the given config directory.
     *
     * @param ConfigurationInterface $config
     * @param null                   $callback
     *
     * @return int
     */
    public function install(ConfigurationInterface $config, $callback = null)
    {
        $proc = $this->execCommand($config, 'install', $callback);

        return $proc->getExitCode();
    }

    /**
     * Creates the cache for the dependency mapping.
     *
     * @param \Sp\BowerBundle\Bower\Configuration $config
     *
     * @throws Exception
     * @return Bower
     */
    public function createDependencyMappingCache(ConfigurationInterface $config)
    {
        $proc = $this->execCommand($config, array('list', '--map'));
        $output = $proc->getOutput();
        if (strpos($output, 'error')) {
            throw new Exception(sprintf('An error occured while creating dependency mapping. The error was %s.', $output));
        }

        $mapping = json_decode($output, true);

        $this->dependencyCache->save($this->createCacheKey($config), $mapping);

        return $this;
    }

    /**
     * Get the dependency mapping from the installed packages.
     *
     * @param Configuration $config
     *
     * @throws Exception
     * @return mixed
     */
    public function getDependencyMapping(ConfigurationInterface $config)
    {
        $cacheKey = $this->createCacheKey($config);
        if (!$this->dependencyCache->contains($cacheKey)) {
            throw new Exception(sprintf('Cached dependencies for "%s" not found, create it with the method createDependencyMappingCache().', $config->getDirectory()));
        }

        return $this->dependencyCache->fetch($cacheKey);
    }

    /**
     * @return ProcessBuilder
     */
    public function getProcessBuilder()
    {
        if (null === $this->processBuilder) {
            return new ProcessBuilder();
        }

        return $this->processBuilder;
    }

    /**
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder
     */
    public function setProcessBuilder(ProcessBuilder $processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }

    /**
     * Creates a bower configuration file (.bowerrc) in the config directory.
     *
     * @param \Sp\BowerBundle\Bower\ConfigurationInterface $configuration The configuration for bower
     */
    protected function dumpBowerConfig(ConfigurationInterface $configuration)
    {
        $configFile = $configuration->getDirectory().DIRECTORY_SEPARATOR.'.bowerrc';
        if (!file_exists($configFile) || file_get_contents($configFile) != $configuration->getJson()) {
            file_put_contents($configFile, $configuration->getJson());
        }
    }

    /**
     * Creates a cache key for the given configuration.
     *
     * @param ConfigurationInterface $config
     *
     * @return string
     */
    private function createCacheKey(ConfigurationInterface $config)
    {
        return hash("sha1", $config->getDirectory());
    }

    /**
     * @param ConfigurationInterface     $config
     * @param string|array               $commands
     * @param \Closure|string|array|null $callback
     *
     * @return \Symfony\Component\Process\Process
     */
    private function execCommand(ConfigurationInterface $config, $commands, $callback = null)
    {
        if (is_string($commands)) {
            $commands = array($commands);
        }

        $event = new BowerEvent($config, $commands);
        $this->eventDispatcher->dispatch(BowerEvents::PRE_EXEC, $event);
        $config =  $event->getConfiguration();

        $this->dumpBowerConfig($config);

        $pb = $this->getProcessBuilder();
        $pb->setWorkingDirectory($config->getDirectory());
        $pb->add($this->bowerPath);
        foreach ($commands as $command) {
            $pb->add($command);
        }

        $proc = $pb->getProcess();
        $proc->run($callback);

        $this->eventDispatcher->dispatch(BowerEvents::POST_EXEC, new BowerEvent($config, $commands));

        return $proc;
    }
}
