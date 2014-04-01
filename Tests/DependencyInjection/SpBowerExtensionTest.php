<?php

/*
 * This file is part of the SpBowerBundle package.
 *
 * (c) Martin Parsiegla <martin.parsiegla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sp\BowerBundle\Tests\DependencyInjection;

use Sp\BowerBundle\DependencyInjection\SpBowerExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Process\ExecutableFinder;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class SpBowerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var SpBowerExtension
     */
    private $extension;

    /**
     * @var string
     */
    private $demoBundlePath;

    public function setUp()
    {
        $this->container = new ContainerBuilder(new ParameterBag(array(
            'kernel.bundles' => array(
                'AsseticBundle' => array(),
                'DemoBundle' => 'Fixtures\Bundles\DemoBundle\DemoBundle'
            ),
            'kernel.cache_dir' => sys_get_temp_dir(),
        )));
        $this->extension = new SpBowerExtension();

        $this->demoBundlePath = __DIR__.'/Fixtures/Bundles/DemoBundle';
        require_once $this->demoBundlePath .'/DemoBundle.php';
    }

    public function testLoadDefaultBin()
    {
        $this->extension->load(array(), $this->container);

        $finder = new ExecutableFinder();
        $bowerLocation = $finder->find('bower', '/usr/bin/bower');

        $this->assertParameter($bowerLocation, 'sp_bower.bower.bin');
    }

    public function testLoadUserBin()
    {
        $binPath = '/some/other/path/to/bower';
        $config = array('sp_bower' => array('bin' => $binPath));
        $this->extension->load($config, $this->container);

        $this->assertParameter($binPath, 'sp_bower.bower.bin');
    }

    public function testLoadDefaultOffline()
    {
        $this->extension->load(array(), $this->container);

        $this->assertParameter(false, 'sp_bower.bower.offline');
    }

    public function testLoadOffline()
    {
        $config = array('sp_bower' => array('offline' => true));
        $this->extension->load($config, $this->container);

        $this->assertParameter(true, 'sp_bower.bower.offline');
    }



    public function testLoadDefaults()
    {
        $config = array(
            'sp_bower' => array(
                'bundles' => array(
                    'DemoBundle' => array(),
                ),
            )
        );

        $this->extension->load($config, $this->container);

        $definition = $this->container->getDefinition('sp_bower.bower_manager');

        $this->assertFalse($this->container->getParameter('sp_bower.install_on_warmup'));
        $this->assertTrue($this->container->has('sp_bower.assetic.config_loader'));
        $this->assertTrue($this->container->has('sp_bower.assetic.bower_resource'));

        $resourceDefinition = $this->container->getDefinition('sp_bower.assetic.bower_resource');
        $resourceMethodCalls = $resourceDefinition->getMethodCalls();
        $this->assertTrue($resourceMethodCalls[2][1][0]);

        // demo bundle assertions
        $calls = $definition->getMethodCalls();
        $this->assertEquals('addBundle', $calls[0][0]);
        $this->assertEquals('DemoBundle', $calls[0][1][0]);
        $configDefinition = $calls[0][1][1];
        $configCalls = $configDefinition->getMethodCalls();
        $this->assertEquals($this->demoBundlePath .'/Resources/config/bower/../../public/components', $configCalls[0][1][0]);
        $this->assertEquals('bower.json', $configCalls[1][1][0]);
        $this->assertEquals('https://bower.herokuapp.com', $configCalls[2][1][0]);

        $cacheDefinition = $this->container->getDefinition('sp_bower.filesystem_cache.demobundle');
        $argumentCall = $cacheDefinition->getArgument(0);
        $this->assertEquals($this->demoBundlePath .'/Resources/config/bower/../../public/components/cache', $argumentCall);
    }

    public function loadDefaultsShouldEnabledNestDependencies()
    {
        $config = array(
            'sp_bower' => array(
                'bundles' => array(
                    'DemoBundle' => array(),
                ),
            )
        );

        $this->extension->load($config, $this->container);

        $resourceDefinition = $this->container->getDefinition('sp_bower.assetic.bower_resource');
        $methodCalls = $resourceDefinition->getMethodCalls();
        $this->assertTrue($methodCalls[2][1][0]);
    }

    public function testLoadUserFilters()
    {
        $jsFilters = array('js_filter');
        $cssFilters = array('css_filter');
        $cssPackageFilters = array('css_package_filter');
        $jsPackageFilters = array('js_package_filter', 'second_js_package_filter');

        $config = array(
            'sp_bower' => array(
                'assetic' => array(
                    'filters' => array(
                        'js' => $jsFilters,
                        'css' => $cssFilters,
                        'packages' => array(
                            'bootstrap' => array(
                                'css' => $cssPackageFilters,
                                'js' => $jsPackageFilters
                            ),
                            'jquery' => array(
                                'css' => $cssPackageFilters,
                                'js' => $jsPackageFilters
                            ),
                        ),
                    ),
                )
            )
        );

        $this->extension->load($config, $this->container);

        $resourceDefinition = $this->container->getDefinition('sp_bower.assetic.bower_resource');
        $resourceCalls = $resourceDefinition->getMethodCalls();
        $this->assertMethodCall($resourceCalls, 'setJsFilters', array($jsFilters));
        $this->assertMethodCall($resourceCalls, 'setCssFilters', array($cssFilters));
        $bootstrapResource = $this->container->getDefinition('sp_bower.assetic.bootstrap_package_resource');
        $this->assertNotNull($bootstrapResource);

        $bootstrapMethodCalls = $bootstrapResource->getMethodCalls();
        $this->assertMethodCall($bootstrapMethodCalls, 'setJsFilters', array($jsPackageFilters));
        $this->assertMethodCall($bootstrapMethodCalls, 'setCssFilters', array($cssPackageFilters));

        $jqueryResource = $this->container->getDefinition('sp_bower.assetic.jquery_package_resource');
        $this->assertNotNull($jqueryResource);

        $jqueryMethodCalls = $jqueryResource->getMethodCalls();
        $this->assertMethodCall($jqueryMethodCalls , 'setJsFilters', array($jsPackageFilters));
        $this->assertMethodCall($jqueryMethodCalls , 'setCssFilters', array($cssPackageFilters));
    }

    public function testBundleAnnotation()
    {
        $config = array(
            'sp_bower' => array(
                'bundles' => array(
                    'DemoBundle' => array(
                        'config_dir' => '@DemoBundle/config',
                        'asset_dir' => '@DemoBundle/assets',
                    ),
                ),
            )
        );

        $this->extension->load($config, $this->container);

        $definition = $this->container->getDefinition('sp_bower.bower_manager');
        $calls = $definition->getMethodCalls();

        // demo bundle assertions
        $configDefinition = $calls[0][1][1];
        $configCalls = $configDefinition->getMethodCalls();
        $this->assertMethodCall($configCalls, 'setAssetDirectory', $this->demoBundlePath .'/assets');
        $this->assertEquals($this->demoBundlePath .'/config', $configDefinition->getArgument(0));
    }

    public function testLoadUserConfig()
    {
        $config = array(
            'sp_bower' => array(
                'assetic' => false,
                'install_on_warmup' => true,
                'bundles' => array(
                    'DemoBundle' => array(
                        'config_dir' => 'Resources/config',
                        'asset_dir' => $this->demoBundlePath .'/Resources/public',
                        'json_file' => 'foo.json',
                        'endpoint' => 'https://bower.example.com',
                        'cache' => 'foobar'
                    ),
                ),
            )
        );

        $this->extension->load($config, $this->container);

        $this->assertTrue($this->container->getParameter('sp_bower.install_on_warmup'));
        $this->assertFalse($this->container->has('sp_bower.assetic.config_loader'));
        $this->assertFalse($this->container->has('sp_bower.assetic.bower_resource'));

        $definition = $this->container->getDefinition('sp_bower.bower_manager');
        $calls = $definition->getMethodCalls();

        $this->assertEquals('DemoBundle', $calls[0][1][0]);
        $configDefinition = $calls[0][1][1];
        $configCalls = $configDefinition->getMethodCalls();
        $this->assertMethodCall($configCalls, 'setAssetDirectory', $this->demoBundlePath .'/Resources/public');
        $this->assertMethodCall($configCalls, 'setJsonFile', 'foo.json');
        $this->assertMethodCall($configCalls, 'setEndpoint', 'https://bower.example.com');
        $this->assertMethodCall($configCalls, 'setCache', array(new Reference('sp_bower.filesystem_cache.demobundle')));
        $cacheDefinition = $this->container->getDefinition('sp_bower.filesystem_cache.demobundle');
        $argumentCall = $cacheDefinition->getArgument(0);
        $this->assertEquals($this->demoBundlePath .'/Resources/config/foobar', $argumentCall);
    }

    /**
     * @test
     */
    public function loadUserCacheShouldSetReference()
    {
        $config = array(
            'sp_bower' => array(
                'assetic' => false,
                'install_on_warmup' => true,
                'bundles' => array(
                    'DemoBundle' => array(
                        'cache' => array(
                            'id' => 'my.service.id'
                        )
                    ),
                ),
            )
        );

        $this->container->setDefinition('my.service.id', new Definition());

        $this->extension->load($config, $this->container);

        $definition = $this->container->getDefinition('sp_bower.bower_manager');
        $calls = $definition->getMethodCalls();

        $configDefinition = $calls[0][1][1];
        $configCalls = $configDefinition->getMethodCalls();
        $this->assertMethodCall($configCalls, 'setCache', array(new Reference('my.service.id')));
    }

    /**
     * @test
     */
    public function loadUserConfigShouldDisableNestDependencies()
    {
        $config = array(
            'sp_bower' => array(
                'bundles' => array(
                    'DemoBundle' => array(),
                ),
                'assetic' => array(
                    'enabled' => true,
                    'nest_dependencies' => false,
                )
            )
        );

        $this->extension->load($config, $this->container);

        $resourceDefinition = $this->container->getDefinition('sp_bower.assetic.bower_resource');
        $methodCalls = $resourceDefinition->getMethodCalls();
        $this->assertMethodCall($methodCalls, 'setNestDependencies', false);
    }

    /**
     * @test
     */
    public function loadUserConfigShouldDisabledNestDependenciesOnPackage()
    {

        $config = array(
            'sp_bower' => array(
                'bundles' => array(
                    'DemoBundle' => array(),
                ),
                'assetic' => array(
                    'enabled' => true,
                    'nest_dependencies' => array('bootstrap' => false)
                )
            )
        );

        $this->extension->load($config, $this->container);

        $resourceDefinition = $this->container->getDefinition('sp_bower.assetic.bootstrap_package_resource');
        $this->assertMethodCall($resourceDefinition->getMethodCalls(), 'setNestDependencies', false);
    }

    /**
     * @param mixed $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->container->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param array $methodCalls
     * @param string $name
     * @param mixed $expectedValues
     */
    private function assertMethodCall(array $methodCalls, $name, $expectedValues)
    {
        if (!is_array($expectedValues)) {
            $expectedValues = array($expectedValues);
        }

        foreach ($methodCalls as $methodCall) {
            if ($methodCall[0] == $name) {
                foreach ($methodCall[1] as $key => $parameter) {
                    $this->assertEquals($expectedValues[$key], $parameter);
                }

                return;
            }
        }

        $this->fail(sprintf('Failed asserting that method %s was called', $name));
    }
}
