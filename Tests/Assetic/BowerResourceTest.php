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
        $this->bowerResource = new BowerResource($this->bower, $this->bowerManager);

        $config = new Configuration('/foo');
        $bundles = array(
            'DemoBundle' => $config,
        );

        $arrayDependencyMapping = require __DIR__ .'/../Bower/Fixtures/dependency_mapping.php';

        $this->bowerManager->expects($this->once())->method('getBundles')->will($this->returnValue($bundles));
        $this->bower->expects($this->once())->method('getDependencyMapping')->with($this->equalTo($config))->will($this->returnValue($arrayDependencyMapping));
    }

    /**
     * @covers Sp\BowerBundle\Assetic\BowerResource::getContent
     */
    public function testGetContent()
    {
        $jsFilters = array('some_js_filter');
        $cssFilters = array('some_css_filter');

        $this->bowerResource->setCssFilters($cssFilters);
        $this->bowerResource->setJsFilters($jsFilters);
        $formulae = $this->bowerResource->getContent();

        $this->assertArrayHasKey('foo_package_css', $formulae);
        $this->assertArrayHasKey('foo_package_js', $formulae);
        $this->assertArrayHasKey('invalid_package_name_js', $formulae);
        $this->assertArrayHasKey('invalid_package_name_css', $formulae);
        $this->assertArrayHasKey('package_css', $formulae);
        $this->assertArrayHasKey('package_js', $formulae);

        $this->assertContains('../components/package/package.js', $formulae['package_js'][0]);
        $this->assertEmpty($formulae['package_css'][0]);

        $this->assertContains('@package_css', $formulae['foo_package_css'][0]);
        $this->assertContains('../components/foo_package/foo.css', $formulae['foo_package_css'][0]);
        $this->assertEquals($cssFilters, $formulae['foo_package_css'][1]);
        $this->assertContains('@package_js', $formulae['foo_package_js'][0]);
        $this->assertEquals($jsFilters, $formulae['foo_package_js'][1]);
    }

    public function testFormulaHasPackageFilters()
    {
        $jsFilter = 'some_js_filter';
        $cssFilter = 'some_css_filter';
        $fooPackageCssFilter = 'foo_package_css_filter';
        $fooPackageJsFilter = 'foo_package_js_filter';
        $packageCssFilter = 'package_css_filter';

        $this->bowerResource->setCssFilters(array($cssFilter));
        $this->bowerResource->setJsFilters(array($jsFilter));
        $this->bowerResource->addPackageCssFilters('foo_package', array($fooPackageCssFilter));
        $this->bowerResource->addPackageJsFilters('foo_package', array($fooPackageJsFilter));
        $this->bowerResource->addPackageCssFilters('package', array($packageCssFilter));

        $formulae = $this->bowerResource->getContent();

        $this->assertContains('@package_css', $formulae['foo_package_css'][0]);
        $this->assertContains($cssFilter, $formulae['foo_package_css'][1]);
        $this->assertContains($fooPackageCssFilter, $formulae['foo_package_css'][1]);
        $this->assertContains($fooPackageJsFilter, $formulae['foo_package_js'][1]);
        $this->assertNotContains($packageCssFilter, $formulae['foo_package_css'][1]);

        $this->assertContains($cssFilter, $formulae['foo_package_css'][1]);
        $this->assertContains($packageCssFilter, $formulae['package_css'][1]);
        $this->assertNotContains($fooPackageCssFilter, $formulae['package_css'][1]);
    }

}
