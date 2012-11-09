<?php

/*
 * This file is part of the Sp/BowerBundle.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Tests\Bower;

use Sp\BowerBundle\Bower\Bower;
use Sp\BowerBundle\Bower\BowerEvent;
use Sp\BowerBundle\Bower\BowerEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileResource;

/**
 * @author Martin Parsiegla <parsiegla@kuponjo.de>
 */
class BowerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bower
     */
    protected $bower;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function setUp()
    {
        if (!isset($_SERVER['BOWER_BIN'])) {
            $this->markTestSkipped('There is no SASS_BIN environment variable.');
        }

        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $this->bower = new Bower($_SERVER['BOWER_BIN'], $this->eventDispatcher);
        $this->target = sys_get_temp_dir() .'/bower_install';
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->target);
    }

    public function tearDown()
    {
        if ($this->filesystem) {
            $this->filesystem->remove($this->target);
        }
    }

    /**
     * @covers Sp\BowerBundle\Bower\Bower::install
     */
    public function testInstall()
    {
        $src = __DIR__ .'/Fixtures';

        $this->bower->install(new DirectoryResource($src), new DirectoryResource($this->target));

        $this->assertFileExists($this->target .'/components');
        $this->assertFileExists($this->target .'/components/jquery');
    }

    public function testPackageInstall()
    {
        $this->bower->install('backbone', new DirectoryResource($this->target));

        $this->assertFileExists($this->target .'/components');
        $this->assertFileExists($this->target .'/components/backbone');
        $this->assertFileExists($this->target .'/components/backbone/backbone.js');
    }

    public function testEventDispatcher()
    {
        $target = new DirectoryResource($this->target);
        $event = new BowerEvent('backbone', $target, Bower::TYPE_PACKAGE);

        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_INSTALL), $this->equalTo($event));
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with($this->equalTo(BowerEvents::POST_INSTALL), $this->equalTo($event));

        $this->bower->install('backbone', $target);
    }
}
