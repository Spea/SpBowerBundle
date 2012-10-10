<?php

/*
 * This file is part of the Sp/BowerBundle.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Bower;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class Bower
{
    protected $bowerPath;

    /**
     * @param string $bowerPath
     */
    public function __construct($bowerPath = '/usr/bin/bower')
    {
        $this->bowerPath = $bowerPath;
    }

    /**
     * Installs bower dependencies from a source directory to a target directory.
     *
     * @param string                                            $src
     * @param string                                            $target
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function install($src, $target, OutputInterface $output = null)
    {
        $target = realpath($target);
        $src = realpath($src);
        if (null === $output) {
            $output = new NullOutput();
        }

        chdir($target);

        $pb = new ProcessBuilder(array($this->bowerPath));
        $pb->add("install");
        $pb->add($src);
        $proc = $pb->getProcess();

        $callback = function($type, $data) use ($output) {
            $output->write($data);
        };

        return $proc->run($callback);
    }
}
