<?php


namespace Sp\BowerBundle\Tests\Bower\Package;


use Sp\BowerBundle\Bower\Configuration;
use Sp\BowerBundle\Bower\Package\DependencyMapper;
use Sp\BowerBundle\Bower\Package\Package;
use Sp\BowerBundle\Tests\Bower\AbstractBowerTest;

class DependencyMapperTest extends AbstractBowerTest
{
    /**
     * @var \Sp\BowerBundle\Bower\Package\DependencyMapper
     */
    private $dependencyMapper;

    public function setUp()
    {
        $this->dependencyMapper = new DependencyMapper();
    }

    public function testMapReturnsPackageDependencies()
    {
        $configDir = self::$fixturesDirectory ."/config";
        $config = new Configuration($configDir);

        $arrayDependencyMapping = require self::$fixturesDirectory .'/dependency_mapping.php';
        $packages = $this->dependencyMapper->map($arrayDependencyMapping, $config);

        $this->assertTrue($packages->containsKey('package'));
        $this->assertTrue($packages->containsKey('other_package'));
        $this->assertTrue($packages->containsKey('dependency_package'));

        /** @var $package Package */
        $package = $packages->get('package');
        $this->assertEquals('package', $package->getName());
        $this->assertCount(1, $package->getScripts());
        $this->assertCount(0, $package->getStyles());
        $this->assertCount(0, $package->getImages());
        $this->assertCount(0, $package->getDependencies());

        /** @var $packageDependency Package */
        $otherPackage = $packages->get('other_package');
        $this->assertEquals('other_package', $otherPackage->getName());
        $this->assertCount(3, $otherPackage->getScripts());
        $this->assertCount(3, $otherPackage->getStyles());
        $this->assertCount(0, $otherPackage->getImages());
        $this->assertCount(1, $otherPackage->getDependencies());

        /** @var $dependencyPackage Package */
        $dependencyPackage = $packages->get('dependency_package');
        $this->assertEquals('dependency_package', $dependencyPackage->getName());
        $this->assertCount(2, $dependencyPackage->getScripts());
        $this->assertCount(2, $dependencyPackage->getStyles());
        $this->assertCount(0, $dependencyPackage->getImages());
        $this->assertCount(2, $dependencyPackage->getDependencies());
    }

    /**
     * @expectedException \Sp\BowerBundle\Bower\Exception\FileNotFoundException
     */
    public function testMapThrowsFileNotFoundException()
    {
        $configDir = self::$fixturesDirectory ."/config";
        $config = new Configuration($configDir);

        $arrayDependencyMapping = require self::$fixturesDirectory .'/invalid_dependency_mapping.php';
        $this->dependencyMapper->map($arrayDependencyMapping, $config);
    }

}
