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

use Sp\BowerBundle\Bower\Bower;
use Sp\BowerBundle\Bower\Configuration;
use Sp\BowerBundle\Bower\Event\BowerEvents;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerTest extends AbstractBowerTest
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

    protected $bin = '/usr/local/bin';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $processBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $process;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dependencyMapper;

    public function setUp()
    {
        $this->target = sys_get_temp_dir() .'/bower_install';
        $this->cache = $this->getMock('Doctrine\Common\Cache\Cache');
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->dependencyMapper = $this->getMock('Sp\BowerBundle\Bower\Package\DependencyMapperInterface');
        $this->bower = $this->getMock(
            'Sp\BowerBundle\Bower\Bower',
            array('dumpBowerConfig'),
            array($this->bin, $this->eventDispatcher, $this->dependencyMapper)
        );
        $this->processBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');
        $this->bower->setProcessBuilder($this->processBuilder);
        $this->process = $this->getMockBuilder('Symfony\Component\Process\Process')->disableOriginalConstructor()->getMock();
    }
    /**
     * @covers Sp\BowerBundle\Bower\Bower::install
     * @dataProvider componentsProvider
     */
    public function testOfflineInstall($configDir)
    {
        $this->bower = $this->getMock(
            'Sp\BowerBundle\Bower\Bower',
            array('dumpBowerConfig'),
            array($this->bin, $this->eventDispatcher, $this->dependencyMapper, true));

        $this->bower->setProcessBuilder($this->processBuilder);
        $this->process->expects($this->once())->method('isSuccessful')->will($this->returnValue(true));

        $config = new Configuration($configDir);
        $config->setCache($this->cache);
        $this->processBuilder->expects($this->at(3))->method('add')->with($this->equalTo('--offline'));
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));

        $this->bower->install($config);
    }

    /**
     * @covers Sp\BowerBundle\Bower\Bower::install
     * @dataProvider componentsProvider
     */
    public function testInstall($configDir)
    {
        $config = new Configuration($configDir);
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('setTimeout')->with($this->equalTo(600));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(3))->method('add')->with($this->equalTo('install'));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));
        $this->process->expects($this->once())->method('isSuccessful')->will($this->returnValue(true));
        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_INSTALL));
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_EXEC));
        $this->eventDispatcher->expects($this->at(2))->method('dispatch')->with($this->equalTo(BowerEvents::POST_EXEC));
        $this->eventDispatcher->expects($this->at(3))->method('dispatch')->with($this->equalTo(BowerEvents::POST_INSTALL));

        $this->bower->expects($this->once())->method('dumpBowerConfig');

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));

        $this->bower->install($config);
    }
    
    /**
     * @covers Sp\BowerBundle\Bower\Bower::update
     * @dataProvider componentsProvider
     */
    public function testUpdate($configDir)
    {
        $config = new Configuration($configDir);
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('setTimeout')->with($this->equalTo(600));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(3))->method('add')->with($this->equalTo('update'));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));
        $this->process->expects($this->once())->method('isSuccessful')->will($this->returnValue(true));
        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_UPDATE));
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_EXEC));
        $this->eventDispatcher->expects($this->at(2))->method('dispatch')->with($this->equalTo(BowerEvents::POST_EXEC));
        $this->eventDispatcher->expects($this->at(3))->method('dispatch')->with($this->equalTo(BowerEvents::POST_UPDATE));

        $this->bower->expects($this->once())->method('dumpBowerConfig');

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));

        $this->bower->update($config);
    }

    /**
     * @covers Sp\BowerBundle\Bower\Bower::createDependencyMappingCache
     */
    public function testCreateDependencyMappingCache()
    {
        $configDir = "/config_dir";
        $config = new Configuration($configDir);
        $config->setCache($this->cache);

        $jsonDependencyMapping = file_get_contents(self::$fixturesDirectory .'/dependency_mapping.json');
        $arrayDependencyMapping = require self::$fixturesDirectory .'/dependency_mapping.php';

        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('setTimeout')->with($this->equalTo(600));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(3))->method('add')->with($this->equalTo('list'));
        $this->processBuilder->expects($this->at(4))->method('add')->with($this->equalTo('--json'));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));
        $this->process->expects($this->once())->method('isSuccessful')->will($this->returnValue(true));
        $this->bower->expects($this->once())->method('dumpBowerConfig');
        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_EXEC));
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with($this->equalTo(BowerEvents::POST_EXEC));

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));
        $this->process->expects($this->once())->method('getOutput')->will($this->returnValue($jsonDependencyMapping));

        $this->cache->expects($this->once())->method('save')->with($this->equalTo(hash('sha1', $configDir)), $this->equalTo($arrayDependencyMapping));

        $this->bower->createDependencyMappingCache($config);
    }

    /**
     * @expectedException \Sp\BowerBundle\Bower\Exception\InvalidMappingException
     */
    public function testCreateDependencyMappingCacheWithInvalidMapping()
    {
        $configDir = "/config_dir";
        $config = new Configuration($configDir);
        $config->setCache($this->cache);

        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));
        $this->process->expects($this->once())->method('isSuccessful')->will($this->returnValue(true));

        $this->cache->expects($this->never())->method('save');

        $this->bower->createDependencyMappingCache($config);
    }

    public function testGetDependencyMapping()
    {
        $configDir = self::$fixturesDirectory ."/config";
        $config = new Configuration($configDir);
        $config->setCache($this->cache);
        $arrayDependencyMapping = require self::$fixturesDirectory .'/simple_dependency_mapping.php';

        $this->cache->expects($this->once())->method('contains')->will($this->returnValue(true));
        $this->cache->expects($this->once())->method('fetch')->will($this->returnValue($arrayDependencyMapping));
        $this->dependencyMapper->expects($this->once())->method('map')->with($this->equalTo($arrayDependencyMapping));

        $this->bower->getDependencyMapping($config);
    }

    /**
     * @expectedException \Sp\BowerBundle\Bower\Exception\RuntimeException
     */
    public function testUnsuccessfulInstallThrowsRuntimeException()
    {
        $jsonString = file_get_contents(self::$fixturesDirectory .'/error.json');
        $configDir = "/config_dir";
        $config = new Configuration($configDir);
        $config->setCache($this->cache);
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));
        $this->process->expects($this->once())->method('isSuccessful')->will($this->returnValue(false));
        $this->process->expects($this->once())->method('getErrorOutput')->will($this->returnValue($jsonString));

        $this->bower->install($config);
    }

    public function componentsProvider()
    {
        return array(
            array(self::$fixturesDirectory .'/config'),
            array(new DirectoryResource('test')),
        );
    }

}
