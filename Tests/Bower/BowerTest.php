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
use Sp\BowerBundle\Bower\BowerEvents;
use Sp\BowerBundle\Bower\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Resource\DirectoryResource;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerTest extends \PHPUnit_Framework_TestCase
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

    public function setUp()
    {
        $this->target = sys_get_temp_dir() .'/bower_install';
        $this->cache = $this->getMock('Doctrine\Common\Cache\Cache');
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
        $this->bower = $this->getMock('Sp\BowerBundle\Bower\Bower', array('dumpBowerConfig'), array($this->bin, $this->cache, $this->eventDispatcher));
        $this->processBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');
        $this->bower->setProcessBuilder($this->processBuilder);
        $this->process = $this->getMockBuilder('Symfony\Component\Process\Process')->disableOriginalConstructor()->getMock();
    }

    /**
     * @covers Sp\BowerBundle\Bower\Bower::install
     * @dataProvider componentsProvider
     */
    public function testInstall($configDir)
    {
        $config = new Configuration($configDir);
        $this->processBuilder->expects($this->at(1))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo('install'));
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));
        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_EXEC));
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with($this->equalTo(BowerEvents::POST_EXEC));

        $this->bower->expects($this->once())->method('dumpBowerConfig');

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));

        $this->bower->install($config);
    }

    /**
     * @covers Sp\BowerBundle\Bower\Bower::createDependencyMappingCache
     */
    public function testCreateDependencyMappingCache()
    {
        $configDir = "/config_dir";
        $config = new Configuration($configDir);

        $jsonDependencyMapping = file_get_contents(__DIR__ .'/Fixtures/dependency_mapping.json');
        $arrayDependencyMapping = require __DIR__ .'/Fixtures/dependency_mapping.php';

        $this->processBuilder->expects($this->at(1))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo('list'));
        $this->processBuilder->expects($this->at(3))->method('add')->with($this->equalTo('--map'));
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));
        $this->bower->expects($this->once())->method('dumpBowerConfig');
        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_EXEC));
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with($this->equalTo(BowerEvents::POST_EXEC));

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));
        $this->process->expects($this->once())->method('getOutput')->will($this->returnValue($jsonDependencyMapping));

        $this->cache->expects($this->once())->method('save')->with($this->equalTo(hash('sha1', $configDir)), $this->equalTo($arrayDependencyMapping));

        $this->bower->createDependencyMappingCache($config);
    }

    public function testCreateDependencyMappingCacheWithInvalidMapping()
    {
        $configDir = "/config_dir";
        $config = new Configuration($configDir);

        $this->processBuilder->expects($this->at(1))->method('add')->with($this->equalTo($this->bin));
        $this->processBuilder->expects($this->at(2))->method('add')->with($this->equalTo('list'));
        $this->processBuilder->expects($this->at(3))->method('add')->with($this->equalTo('--map'));
        $this->processBuilder->expects($this->once())->method('setWorkingDirectory')->with($this->equalTo($configDir));
        $this->processBuilder->expects($this->once())->method('getProcess')->will($this->returnValue($this->process));
        $this->bower->expects($this->once())->method('dumpBowerConfig');
        $this->eventDispatcher->expects($this->at(0))->method('dispatch')->with($this->equalTo(BowerEvents::PRE_EXEC));
        $this->eventDispatcher->expects($this->at(1))->method('dispatch')->with($this->equalTo(BowerEvents::POST_EXEC));

        $this->process->expects($this->once())->method('run')->with($this->equalTo(null));
        $this->process->expects($this->once())->method('getOutput')->will($this->returnValue(""));

        $this->cache->expects($this->never())->method('save');
        $this->cache->expects($this->once())->method('delete')->with($this->equalTo(hash('sha1', $configDir)));

        $this->bower->createDependencyMappingCache($config);
    }

    public function testGetDependencyMapping()
    {
        $configDir = __DIR__ ."/Fixtures/config";
        $config = new Configuration($configDir);
        $arrayDependencyMapping = require __DIR__ .'/Fixtures/simple_dependency_mapping.php';

        $this->cache->expects($this->once())->method('contains')->will($this->returnValue(true));
        $this->cache->expects($this->once())->method('fetch')->will($this->returnValue($arrayDependencyMapping));

        $mapping = $this->bower->getDependencyMapping($config);
        $this->assertCount(1, $mapping);
        $this->assertArrayHasKey('source', $mapping['simple_package']);
        $this->assertArrayHasKey('main', $mapping['simple_package']['source']);
        $source = $mapping['simple_package']['source'];
        $this->assertCount(3, $source['main']);
        $this->assertEquals(__DIR__ ."/Fixtures/components/simple_package/styles.css", $source['main'][0]);
        $this->assertEquals(__DIR__ ."/Fixtures/components/simple_package/script.js", $source['main'][1]);
        $this->assertEquals("", $source['main'][2]);
    }

    /**
     * @expectedException \Sp\BowerBundle\Bower\FileNotFoundException
     */
    public function testGetDependencyMappingThrowsFileNotFoundException()
    {
        $configDir = __DIR__ ."/Fixtures/config";
        $config = new Configuration($configDir);
        $arrayDependencyMapping = require __DIR__ .'/Fixtures/dependency_mapping.php';

        $this->cache->expects($this->once())->method('contains')->will($this->returnValue(true));
        $this->cache->expects($this->once())->method('fetch')->will($this->returnValue($arrayDependencyMapping));

        $this->bower->getDependencyMapping($config);
    }

    public function componentsProvider()
    {
        return array(
            array(__DIR__ .'/Fixtures/config'),
            array(new DirectoryResource('test')),
        );
    }

}
