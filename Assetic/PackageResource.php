<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Assetic;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sp\BowerBundle\Bower\Exception\InvalidArgumentException;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class PackageResource implements \Serializable
{
    /**
     * @var Collection
     */
    private $cssFilters;

    /**
     * @var Collection
     */
    private $jsFilters;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $nestDependencies;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->jsFilters = new ArrayCollection();
        $this->cssFilters = new ArrayCollection();
    }

    /**
     * @param Collection|array $jsFilters
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setJsFilters($jsFilters)
    {
        if (is_array($jsFilters)) {
            $jsFilters = new ArrayCollection($jsFilters);
        } elseif (!($jsFilters instanceof Collection)) {
            throw new InvalidArgumentException(
                '$packageCssFilters must be an array or an instance of \Doctrine\Commons\Collections\Collection'
            );
        }

        $this->jsFilters = $jsFilters;

        return $this;
    }

    /**
     * @param string $filter
     *
     * @return $this
     */
    public function addJsFilter($filter)
    {
        $this->jsFilters->add($filter);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getJsFilters()
    {
        return $this->jsFilters;
    }

    /**
     * @param Collection|array $cssFilters
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function setCssFilters($cssFilters)
    {
        if (is_array($cssFilters)) {
            $cssFilters = new ArrayCollection($cssFilters);
        } elseif (!($cssFilters instanceof Collection)) {
            throw new InvalidArgumentException(
                '$packageCssFilters must be an array or an instance of \Doctrine\Commons\Collections\Collection'
            );
        }

        $this->cssFilters = $cssFilters;

        return $this;
    }

    /**
     * @param string $filter
     *
     * @return $this
     */
    public function addCssFilter($filter)
    {
        $this->cssFilters->add($filter);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCssFilters()
    {
        return $this->cssFilters;
    }

    /**
     * @param bool $nestedDependencies
     *
     * @return $this
     */
    public function setNestDependencies($nestedDependencies)
    {
        $this->nestDependencies = $nestedDependencies;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldNestDependencies()
    {
        return $this->nestDependencies;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->name, $this->jsFilters, $this->cssFilters, $this->nestDependencies));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->name, $this->jsFilter, $this->cssfiltesr, $this->nestDependencies) = unserialize($serialized);
    }
}
