<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Composer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\Event;

/**
 * Script handler for installing bower dependencies on specific composer events.
 *
 * @author Francisco Facioni <fran6co@gmail.com>
 */
class ScriptHandler
{
    /**
     * @param \Composer\Script\Event $event
     */
    public static function bowerInstall(Event $event)
    {
        $options = self::getOptions($event);

        $binDir = $options['symfony-app-dir'];
        $configKey = 'symfony-app-dir';

        if (isset($options['symfony-bin-dir'])) {
            $binDir = $options['symfony-bin-dir'];
            $configKey = 'symfony-bin-dir';
        }

        if (!is_dir($binDir)) {
            echo 'The '.$configKey.' ('.$binDir.') specified in composer.json was not found in '.getcwd().', can not install Bower dependencies.'.PHP_EOL;

            return;
        }

        static::executeCommand($event, $binDir, 'sp:bower:install', $options['process-timeout']);
    }

    /**
     * @param \Composer\Script\Event $event
     * @param string                 $appDir
     * @param string                 $cmd
     * @param int                    $timeout
     *
     * @throws \RuntimeException
     */
    protected static function executeCommand(Event $event, $appDir, $cmd, $timeout = 300)
    {
        $php = escapeshellarg(self::getPhp());
        $console = escapeshellarg($appDir.'/console');
        if ($event->getIO()->isDecorated()) {
            $console.= ' --ansi';
        }

        $process = new Process($php.' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) { echo $buffer; });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', escapeshellarg($cmd)));
        }
    }

    /**
     * @param \Composer\Script\Event $event
     *
     * @return array
     */
    protected static function getOptions(Event $event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
        ), $event->getComposer()->getPackage()->getExtra());

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

    /**
     * @return string|false
     * @throws \RuntimeException
     */
    protected static function getPhp()
    {
        $phpFinder = new PhpExecutableFinder;
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }
}
