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

use Sp\BowerBundle\Bower\Event\BowerCommandEvent;
use Sp\BowerBundle\Bower\Event\BowerEvent;
use Sp\BowerBundle\Bower\Event\BowerEvents;
use Sp\BowerBundle\Bower\Exception\CommandException;
use Sp\BowerBundle\Bower\Exception\InvalidMappingException;
use Sp\BowerBundle\Bower\Exception\RuntimeException;
use Sp\BowerBundle\Bower\Package\DependencyMapper;
use Sp\BowerBundle\Bower\Package\DependencyMapperInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\ProcessBuilder;

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
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Sp\BowerBundle\Bower\Package\DependencyMapperInterface
     */
    protected $dependencyMapper;

    /**
     * @var boolean true if bower should operate in offline mode
     */
    protected $offline;

    /**
     * @var boolean true if bower should operate in allow-root mode
     */
    protected $allowRoot;

    /**
     * @param string                                                      $bowerPath
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param Package\DependencyMapperInterface                           $dependencyMapper
     * @param boolean                                                     $offline
     * @param boolean                                                     $allowRoot
     */
    public function __construct($bowerPath = '/usr/bin/bower', EventDispatcherInterface $eventDispatcher,
                                DependencyMapperInterface $dependencyMapper = null, $offline = false, $allowRoot = false)
    {
        $this->bowerPath = $bowerPath;
        $this->eventDispatcher = $eventDispatcher;
        $this->dependencyMapper = $dependencyMapper ?: new DependencyMapper();
        $this->offline = $offline;
        $this->allowRoot = $allowRoot;
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
        $this->eventDispatcher->dispatch(BowerEvents::PRE_INSTALL, new BowerEvent($config));

        $result = $this->execCommand($config, array('install'), $callback);

        $this->eventDispatcher->dispatch(BowerEvents::POST_INSTALL, new BowerEvent($config));

        return $result->getProcess()->getExitCode();
    }
    
    /**
     * Updates bower dependencies from the given config directory.
     *
     * @param ConfigurationInterface $config
     * @param null                   $callback
     *
     * @return int
     */
    public function update(ConfigurationInterface $config, $callback = null)
    {
        $this->eventDispatcher->dispatch(BowerEvents::PRE_UPDATE, new BowerEvent($config));

        $result = $this->execCommand($config, array('update'), $callback);

        $this->eventDispatcher->dispatch(BowerEvents::POST_UPDATE, new BowerEvent($config));

        return $result->getProcess()->getExitCode();
    }

    /**
     * Creates the cache for the dependency mapping.
     *
     * @param \Sp\BowerBundle\Bower\ConfigurationInterface $config
     *
     * @throws Exception\MappingException
     * @throws Exception\InvalidMappingException
     * @return Bower
     */
    public function createDependencyMappingCache(ConfigurationInterface $config)
    {
        $result = $this->execCommand($config, array('list', '--json'));
        $output = $result->getProcess()->getOutput();

        $mapping = json_decode($output, true);
        if (null === $mapping) {
            throw new InvalidMappingException('Bower returned an invalid dependency mapping. This mostly happens when the dependencies are not yet installed or if you are using an old version of bower.');
        }

        $cacheKey = $this->createCacheKey($config);
        $config->getCache()->save($cacheKey, $mapping);

        return $this;
    }

    /**
     * Get the dependency mapping from the installed packages.
     *
     * @param \Sp\BowerBundle\Bower\ConfigurationInterface $config
     *
     * @throws Exception\RuntimeException
     * @return mixed
     */
    public function getDependencyMapping(ConfigurationInterface $config)
    {
        $cacheKey = $this->createCacheKey($config);

        $dependencyCache = $config->getCache();
        if (!$dependencyCache->contains($cacheKey)) {
            throw new RuntimeException(sprintf(
                'Cached dependencies for "%s" not found, create it with the method createDependencyMappingCache().', $config->getDirectory()
            ));
        }

        $mapping = $dependencyCache->fetch($cacheKey);

        return $this->dependencyMapper->map($mapping, $config);
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
        $configFile = $configuration->getDirectory() . DIRECTORY_SEPARATOR . '.bowerrc';
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
     * @throws Exception\CommandException
     * @return BowerResult
     */
    private function execCommand(ConfigurationInterface $config, $commands, $callback = null)
    {
        if (is_string($commands)) {
            $commands = array($commands);
        }

        $event = new BowerCommandEvent($config, $commands);
        $this->eventDispatcher->dispatch(BowerEvents::PRE_EXEC, $event);
        $config = $event->getConfiguration();

        $this->dumpBowerConfig($config);

        $pb = $this->getProcessBuilder();
        $pb->setWorkingDirectory($config->getDirectory());
        $pb->setTimeout(600);
        $pb->add($this->bowerPath);
        if ($this->offline) {
            $pb->add('--offline');
        }
        if ($this->allowRoot) {
            $pb->add('--allow-root');
        }

        foreach ($commands as $command) {
            $pb->add($command);
        }

        $proc = $pb->getProcess();
        $proc->run($callback);

        if (!$proc->isSuccessful()) {
            throw new CommandException($proc->getCommandLine(),trim($proc->getErrorOutput()));
        }

        $this->eventDispatcher->dispatch(BowerEvents::POST_EXEC, new BowerCommandEvent($config, $commands));

        return new BowerResult($proc, $config);
    }
}
