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

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class BowerManager
{
    /**
     * @var Bower
     */
    protected $bower;

    /**
     * @var array
     */
    protected $components;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param Bower $bower
     */
    public function __construct(Bower $bower)
    {
        $this->bower = $bower;
    }

    /**
     * @param string $name
     * @param string $src
     * @param null   $target
     */
    public function addComponent($name, $src, $target = null)
    {
        if (null === $target) {
            $target = $src;
        }

        $this->components[$name] = array(
            'src' => $src,
            'target' => $target
        );
    }

    /**
     *
     */
    public function install()
    {
        foreach ($this->components as $component) {
            $this->bower->install($component['src'], $component['target'], $this->getOutput());
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
}
