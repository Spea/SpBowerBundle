<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Tests\Assetic;

use Sp\BowerBundle\Bower\Configuration;
use Sp\BowerBundle\Assetic\BowerResource;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class BowerResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BowerResource
     */
    protected $bowerResource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $bower;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $bowerManager;

    protected function setUp()
    {
        $this->bower = $this->getMockBuilder('Sp\BowerBundle\Bower\Bower')->disableOriginalConstructor()->getMock();
        $this->bowerManager = $this->getMockBuilder('Sp\BowerBundle\Bower\BowerManager')->disableOriginalConstructor()->getMock();
        $this->bowerResource = $this->getMock('Sp\BowerBundle\Assetic\BowerResource', array('resolvePaths'), array($this->bower, $this->bowerManager));
        $this->bowerResource->expects($this->any())->method('resolvePaths')->will($this->returnCallback(function ($configDir, $files) {
            return $files;
        }));
    }

    /**
     * @covers Sp\BowerBundle\Assetic\BowerResource::getContent
     */
    public function testGetContent()
    {
        $paths = array(
            '/foo' => new Configuration(),
        );

        $arrayDependencyMapping = require __DIR__ .'/../Bower/Fixtures/dependency_mapping.php';

        $this->bowerManager->expects($this->once())->method('getPaths')->will($this->returnValue($paths));
        $this->bower->expects($this->once())->method('getDependencyMapping')->with($this->equalTo('/foo'))->will($this->returnValue($arrayDependencyMapping));

        $formulae = $this->bowerResource->getContent();

        $this->assertArrayHasKey('foo_package_css', $formulae);
        $this->assertArrayHasKey('foo_package_js', $formulae);
        $this->assertArrayHasKey('package_css', $formulae);
        $this->assertArrayHasKey('package_js', $formulae);

        $this->assertContains('../components/package/package.js', $formulae['package_js'][0]);
        $this->assertEmpty($formulae['package_css'][0]);

        $this->assertContains('@package_css', $formulae['foo_package_css'][0]);
        $this->assertContains('../components/foo_package/foo.css', $formulae['foo_package_css'][0]);
        $this->assertContains('@package_js', $formulae['foo_package_js'][0]);
        $this->assertContains('../components/foo_package/barfoo.js', $formulae['foo_package_js'][0]);
    }

}
