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
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;
use Doctrine\Common\Collections\Collection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $components;

    /**
     * @param Bower $bower
     */
    public function __construct(Bower $bower)
    {
        $this->bower = $bower;
        $this->components = new ArrayCollection();
    }


    /**
     * @param \Symfony\Component\Config\Resource\DirectoryResource $src
     * @param \Symfony\Component\Config\Resource\DirectoryResource $target
     */
    public function addComponent(DirectoryResource $src, DirectoryResource $target )
    {
        $this->components->add(array(
            'src' => $src,
            'target' => $target
        ));
    }

    /**
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
    }

}
