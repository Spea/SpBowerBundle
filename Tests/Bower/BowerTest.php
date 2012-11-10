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
     * @var \PHPUnit_Framework_MockObject_MockObject
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

    protected $bin = '/usr/local/bin';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $processBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected  $process;

    public function setUp()
    {
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $this->target = sys_get_temp_dir() .'/bower_install';
        $this->bower = $this->getMock('Sp\BowerBundle\Bower\Bower', array('getProcessBuilder'), array($this->bin, $this->eventDispatcher));
        $this->processBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');
        $this->process = $this->getMockBuilder('Symfony\Component\Process\Process')->disableOriginalConstructor()->getMock();
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
     * @dataProvider componentsProvider
     */
    public function testInstall($source, $target, $type)
    {
        $target = new DirectoryResource($target);

        $this->bower->expects($this->once())->method('getProcessBuilder')->will($this->returnValue($this->processBuilder));

        $this->processBuilder->expects($this->at(1))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo('install'));
        $this->processBuilder->expects($this->at(3))->method('add')->with($this->equalTo($source));
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($target));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));

        $event = new BowerEvent($source, $target, $type);

        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_INSTALL), $this->equalTo($event));
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with($this->equalTo(BowerEvents::POST_INSTALL), $this->equalTo($event));

        $this->bower->install($source, $target);
    }

    public function componentsProvider()
    {
        return array(
            array(new DirectoryResource(__DIR__ .'/Fixtures'), $this->target, Bower::TYPE_FILE),
            array('backbone', $this->target, Bower::TYPE_PACKAGE),
            array('jquery#1.8.1', $this->target, Bower::TYPE_PACKAGE),
        );
    }

    public function testWrongArgumentThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException', 'The source must be a string or an instance of DirectoryResource');

        $this->bower->install(new \stdClass(), new DirectoryResource($this->target));
    }
}
