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
        $result = $this->execCommand($config, 'install', $callback);

        return $result->getProcess()->getExitCode();
    }

    /**
     * Creates the cache for the dependency mapping.
     *
     * @param \Sp\BowerBundle\Bower\ConfigurationInterface $config
     *
     * @throws Exception
     * @return Bower
     */
    public function createDependencyMappingCache(ConfigurationInterface $config)
    {
        $result = $this->execCommand($config, array('list', '--map'));
        $output = $result->getProcess()->getOutput();
        if (strpos($output, 'error')) {
            throw new Exception(sprintf('An error occured while creating dependency mapping. The error was %s.', $output));
        }

        $mapping = json_decode($output, true);
        $cacheKey = $this->createCacheKey($result->getConfig());
        if (null === $mapping) {
            $this->dependencyCache->delete($cacheKey);

            return $this;
        }

        $this->dependencyCache->save($cacheKey, $mapping);

        return $this;
    }

    /**
     * Get the dependency mapping from the installed packages.
     *
     * @param \Sp\BowerBundle\Bower\ConfigurationInterface $config
     *
     * @throws Exception
     * @return mixed
     */
    public function getDependencyMapping(ConfigurationInterface $config)
    {
        $event = new BowerEvent($config, array());
        $this->eventDispatcher->dispatch(BowerEvents::PRE_EXEC, $event);
        $config =  $event->getConfiguration();

        $cacheKey = $this->createCacheKey($config);
        if (!$this->dependencyCache->contains($cacheKey)) {
            throw new Exception(sprintf('Cached dependencies for "%s" not found, create it with the method createDependencyMappingCache().', $config->getDirectory()));
        }

        $this->eventDispatcher->dispatch(BowerEvents::POST_EXEC, new BowerEvent($config, array()));

        $mapping = $this->dependencyCache->fetch($cacheKey);

        // Make sure to have an absolute path for all sources.
        foreach ($mapping as $packageName => $package) {
            if (isset($package['source']['main'])) {
                $files = $package['source']['main'];
                if (is_string($files)) {
                    $mapping[$packageName]['source']['main']  = $this->resolvePath($config->getDirectory(), $files);
                } else {
                    foreach ($files as $key => $source) {
                        $mapping[$packageName]['source']['main'][$key] = $this->resolvePath($config->getDirectory(), $source);
                    }
                }
            }
        }

        return $mapping;
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
     * @return BowerResult
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

        return new BowerResult($proc, $config);
    }

    /**
     * Creates an absolute path for all passed files based on the config directory..
     *
     * @param string $configDir
     * @param string $file
     *
     * @throws FileNotFoundException
     * @return string
     */
    protected function resolvePath($configDir, $file)
    {
        chdir($configDir);
        if (strpos($file, '@') === 0) {
            return $file;
        }

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (!file_exists($file) && in_array($extension, array('json', 'css'))) {
            throw new FileNotFoundException(
                sprintf('The required file "%s" could not be found. Did you accidentally deleted the "components" directory?', $configDir ."/".$file)
            );
        }

        return realpath($file) ?: "";
    }
}
