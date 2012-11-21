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
    protected $cache;

    /**
     * @param string                       $bowerPath
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct($bowerPath = '/usr/bin/bower', Cache $cache)
    {
        $this->bowerPath = $bowerPath;
        $this->cache = $cache;
    }

    /**
     * Installs bower dependencies from the given config directory.
     *
     * @param string $configDir
     * @param null  $callback
     *
     * @return int
     */
    public function install(Configuration $config, $callback = null)
    {
        $proc = $this->execCommand($config->getDirectory(), 'install', $callback);

        return $proc->getExitCode();
    }

    /**
     * Creates a bower configuration file (.bowerrc) in the config directory.
     *
     * @param Configuration $configuration The configuration for bower
     */
    public function init(Configuration $configuration)
    {
        file_put_contents($configuration->getDirectory().DIRECTORY_SEPARATOR.'.bowerrc', $configuration->getJson());
    }

    /**
     * Creates the cache for the dependency mapping.
     *
     * @param \Sp\BowerBundle\Bower\Configuration $config
     *
     * @throws Exception
     * @return \Sp\BowerBundle\Bower\Bower
     */
    public function createDependencyMappingCache(Configuration $config)
    {
        $proc = $this->execCommand($config->getDirectory(), array('list', '--map'));
        $output = $proc->getOutput();
        if (strpos($output, 'error')) {
            throw new Exception(sprintf('An error occured while creating dependency mapping. The error was %s.', $output));
        }

        $mapping = json_decode($output, true);

        $this->cache->save($this->createCacheKey($config), $mapping);

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
    public function getDependencyMapping(Configuration $config)
    {
        if (!$this->cache->contains($config)) {
            throw new Exception(sprintf('Cached dependencies for "%s" not found, create it with the method createDependencyMappingCache().', $config));
        }

        return $this->cache->fetch($this->createCacheKey($config));
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

    private function createCacheKey(Configuration $config)
    {
        return hash("sha1", $config->getDirectory());
    }

    /**
     * @param string                $configDir
     * @param string|array          $commands
     * @param \Closure|string|array|null $callback
     *
     * @return \Symfony\Component\Process\Process
     */
    private function execCommand($configDir, $commands, $callback = null)
    {
        if (is_string($commands)) {
            $commands = array($commands);
        }

        $pb = $this->getProcessBuilder();
        $pb->setWorkingDirectory($configDir);
        $pb->add($this->bowerPath);
        foreach ($commands as $command) {
            $pb->add($command);
        }

        $proc = $pb->getProcess();
        $proc->run($callback);

        return $proc;
    }
}
