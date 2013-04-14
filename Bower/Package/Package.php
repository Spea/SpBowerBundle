<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Bower\Package;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class Package
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $styles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $scripts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $images;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $dependencies;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->scripts = new ArrayCollection();
        $this->styles = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->dependencies = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $script
     *
     * @return $this
     */
    public function addScript($script)
    {
        if (!$this->scripts->contains($script)) {
            $this->scripts->add($script);
        }

        return $this;
    }

    /**
     * @param array $scripts
     *
     * @return $this
     */
    public function addScripts(array $scripts)
    {
        foreach ($scripts as $script) {
            $this->addScript($script);
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * @param string $style
     *
     * @return $this
     */
    public function addStyle($style)
    {
        if (!$this->styles->contains($style)) {
            $this->styles->add($style);
        }

        return $this;
    }

    /**
     * @param array $styles
     *
     * @return $this
     */
    public function addStyles(array $styles)
    {
        foreach ($styles as $style) {
            $this->addStyle($style);
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @param \Sp\BowerBundle\Bower\Package\Package $dependency
     *
     * @return $this
     */
    public function addDependency(Package $dependency)
    {
        $this->dependencies->add($dependency);

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param string $image
     *
     * @return $this
     */
    public function addImage($image)
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }

        return $this;
    }

    /**
     * @param array $images
     *
     * @return $this
     */
    public function addImages(array $images)
    {
        foreach ($images as $image) {
            $this->addImage($image);
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }
}
