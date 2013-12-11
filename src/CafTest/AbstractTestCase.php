<?php

namespace CafTest;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend\ServiceManager\ServiceManager
     */
    protected static $serviceManager;

    public static function setUpBeforeClass()
    {

        $path = stream_resolve_include_path(__DIR__ . '/../../../../../vendor');

        shell_exec($path . '/bin/doctrine-module orm:schema-tool:drop --force');
        shell_exec($path . '/bin/doctrine-module orm:schema-tool:create');

        $pathDir = getcwd()."/";

        $config = include $pathDir.'config/test.config.php';

        self::$serviceManager = new ServiceManager(
            new ServiceManagerConfig(
                isset($config['service_manager']) ? $config['service_manager'] : array()
            )
        );
        self::$serviceManager->setService('ApplicationConfig', $config);
        self::$serviceManager->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');

        $moduleManager = self::$serviceManager->get('ModuleManager');
        $moduleManager->loadModules();
    }

    public function setup()
    {
        parent::setup();
        $this->application = self::$serviceManager->get('Application');
    }

    public function tearDown()
    {
        parent::tearDown();

    }

}
