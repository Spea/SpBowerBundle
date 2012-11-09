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
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class Bower
{
    /**
     * @var string
     */
    protected $bowerPath;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param string                                             $bowerPath
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher
     */
    public function __construct($bowerPath = '/usr/bin/bower', EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->bowerPath = $bowerPath;
    }

    /**
     * Installs bower dependencies from a source directory to a target directory.
     *
     * @param string|\Symfony\Component\Config\Resource\DirectoryResource $source
     * @param \Symfony\Component\Config\Resource\DirectoryResource        $target
     * @param null                                                        $callback
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    public function install($source, DirectoryResource $target, $callback = null)
    {
        if (!is_string($source) && !$source instanceof DirectoryResource) {
            throw new \InvalidArgumentException('The source must be a string or an instance of DirectoryResource');
        }

        $this->eventDispatcher->dispatch(BowerEvents::PRE_INSTALL);

        $pb = new ProcessBuilder(array($this->bowerPath));
        $pb->setWorkingDirectory($target);
        $pb->add("install");
        $pb->add($source);
        $proc = $pb->getProcess();


        return $proc->run($callback);
    }
}
