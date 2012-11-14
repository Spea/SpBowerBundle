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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @param string $bowerPath
     */
    public function __construct($bowerPath = '/usr/bin/bower')
    {
        $this->bowerPath = $bowerPath;
    }

    /**
     * Installs bower dependencies from the given config directory.
     *
     * @param string $configDir
     * @param null  $callback
     *
     * @return int
     */
    public function install($configDir, $callback = null)
    {
        $proc = $this->execCommand($configDir, 'install', $callback);

        return $proc->getExitCode();
    }

    /**
     * Creates a bower configuration file (.bowerrc) in the specified directory.
     *
     * @param string        $configDir     The directory where the configuration file (.bowerrc) should be placed.
     * @param Configuration $configuration The configuration for bower
     */
    public function init($configDir, Configuration $configuration)
    {
        file_put_contents($configDir.DIRECTORY_SEPARATOR.'.bowerrc', $configuration->getJson());
    }

    /**
     * Get the dependency mapping from the installed packages.
     *
     * @param string $configDir
     *
     * @return array
     */
    public function getDependencyMapping($configDir)
    {
        $proc = $this->execCommand($configDir, array('list', '--map'));
        $output = $proc->getOutput();
        if (strpos($output, 'error')) {
            return array();
        }

        return json_decode($output, true);
    }

    /**
     * @return \Symfony\Component\Process\ProcessBuilder
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
     * @param string                $configDir
     * @param string|array          $commands
     * @param \Closure|string|array $callback
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
