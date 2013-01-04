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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class SpBowerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    /**
     * @var SpBowerExtension
     */
    private $extension;

    public function setUp()
    {
        $this->container = new ContainerBuilder(new ParameterBag(array(
            'kernel.bundles' => array(
                'AsseticBundle' => array(),
            )
        )));
        $this->extension = new SpBowerExtension();
    }

    public function testLoadDefaultBin()
    {
        $this->extension->load(array(), $this->container);

        $this->assertParameter('/usr/bin/bower', 'sp_bower.bower.bin');
    }

    public function testLoadUserBin()
    {
        $binPath = '/usr/local/bin/bower';
        $config = array('sp_bower' => array('bin' => $binPath));
        $this->extension->load($config, $this->container);

        $this->assertParameter($binPath, 'sp_bower.bower.bin');
    }

    public function testLoadDefaults()
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        $bundles['DemoBundle'] = 'Fixtures\Bundles\DemoBundle\DemoBundle';
        $this->container->setParameter('kernel.bundles', $bundles);

        $demoBundlePath = __DIR__.'/Fixtures/Bundles/DemoBundle';
        require_once $demoBundlePath .'/DemoBundle.php';

        $config = array(
            'sp_bower' => array(
                'bundles' => array(
                    'DemoBundle' => array(),
                ),
            )
        );

        $this->extension->load($config, $this->container);

        $definition = $this->container->getDefinition('sp_bower.bower_manager');
        $calls = $definition->getMethodCalls();

        $this->assertFalse($this->container->getParameter('sp_bower.install_on_warmup'));
        $this->assertTrue($this->container->has('sp_bower.assetic.config_loader'));
        $this->assertTrue($this->container->has('sp_bower.assetic.bower_resource'));

        // demo bundle assertions
        $this->assertEquals('addBundle', $calls[0][0]);
        $this->assertEquals('DemoBundle', $calls[0][1][0]);
        $configDefinition = $calls[0][1][1];
        $configCalls = $configDefinition->getMethodCalls();
        $this->assertEquals('../../public/components', $configCalls[0][1][0]);
        $this->assertEquals('component.json', $configCalls[1][1][0]);
        $this->assertEquals('https://bower.herokuapp.com', $configCalls[2][1][0]);
    }

    public function testLoadUserConfig()
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        $bundles['DemoBundle'] = 'Fixtures\Bundles\DemoBundle\DemoBundle';
        $this->container->setParameter('kernel.bundles', $bundles);

        $demoBundlePath = __DIR__.'/Fixtures/Bundles/DemoBundle';
        require_once $demoBundlePath .'/DemoBundle.php';

        $config = array(
            'sp_bower' => array(
                'register_assets' => false,
                'install_on_warmup' => true,
                'bundles' => array(
                    'DemoBundle' => array(
                        'config_dir' => 'Resources/config',
                        'asset_dir' => $demoBundlePath .'/Resources/public',
                        'json_file' => 'foo.json',
                        'endpoint' => 'https://bower.example.com',
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
        $this->assertEquals('../public/', $configCalls[0][1][0]);
        $this->assertEquals('foo.json', $configCalls[1][1][0]);
        $this->assertEquals('https://bower.example.com', $configCalls[2][1][0]);
    }

    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->container->getParameter($key), sprintf('%s parameter is correct', $key));
    }
}
