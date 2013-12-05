<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Tests\Bower;

use Doctrine\Common\Cache\FilesystemCache;
use Sp\BowerBundle\Bower\Bower;
use Doctrine\Common\Cache\ArrayCache;
use Sp\BowerBundle\Bower\Configuration;
use Sp\BowerBundle\EventListener\CacheCreateListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerFunctionalTest extends AbstractBowerTest
{
    /**
     * @var Bower
     */
    protected $bower;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function setUp()
    {
        if (!isset($_SERVER['BOWER_BIN'])) {
            $this->markTestSkipped('There is no BOWER_BIN environment variable.');
        }

        $this->target = sys_get_temp_dir() .'/bower_install_'. uniqid();
        $this->eventDispatcher = new EventDispatcher();
        $this->bower = new Bower($_SERVER['BOWER_BIN'], $this->eventDispatcher);
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->target);
    }

    /**
     * @test
     */
    public function installShouldCreateComponentsDirectory()
    {
        $src = self::$fixturesDirectory .'/config';
        $configuration = $this->createConfig($src);

        $this->bower->install($configuration);

        $this->assertFileExists($this->target .'/components');
        $this->assertFileExists($this->target .'/components/jquery');
        $this->assertFileExists($this->target .'/components/jquery/jquery.js');
    }

    /**
     * @test
     */
    public function installShouldCreateCacheDir()
    {
        $src = self::$fixturesDirectory .'/config';

        $configuration = $this->createConfig($src);
        $this->eventDispatcher->addSubscriber(new CacheCreateListener($this->bower));

        $this->bower->install($configuration);

        $this->assertFileExists($this->target .'/cache');
    }

    /**
     * @param string $src
     *
     * @return Configuration
     */
    private function createConfig($src)
    {
        $configuration = new Configuration($src);
        $configuration->setJsonFile('component.json');
        $configuration->setEndpoint('https://bower.herokuapp.com');
        $configuration->setCache(new FilesystemCache($this->target .'/cache'));
        $configuration->setAssetDirectory($this->filesystem->makePathRelative($this->target . '/components', $src));

        return $configuration;
    }
}
