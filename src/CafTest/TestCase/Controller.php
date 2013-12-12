<?php

namespace CafTest\TestCase;

use CafTest\AbstractTestCase;

use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;

abstract class Controller extends AbstractTestCase
{
    /**
     * The ActionController we are testing
     *
     * @var Zend\Mvc\Controller\AbstractActionController
     */
    protected $controller;

    /**
     * A request object
     *
     * @var Zend\Http\Request
     */
    protected $request;

    /**
     * A response object
     *
     * @var Zend\Http\Response
     */
    protected $response;

    /**
     * The matched route for the controller
     *
     * @var Zend\Mvc\Router\RouteMatch
     */
    protected $routeMatch;

    /**
     * An MVC event to be assigned to the controller
     *
     * @var Zend\Mvc\MvcEvent
     */
    protected $event;

    /**
     * The Controller fully qualified domain name, so each ControllerTestCase can create an instance
     * of the tested controller
     *
     * @var string
     */
    protected $controllerFQDN;

    /**
     * The route to the controller, as defined in the configuration files
     *
     * @var string
     */
    protected $controllerRoute;

    public function setup()
    {
        parent::setup();

        $this->routes = $this->loadRoutes();

        $this->serviceManager->setAllowOverride(true);

        $this->event = new MvcEvent();
        $this->event->setTarget($this->application);
        $this->event->setApplication($this->application)
            ->setRequest($this->application->getRequest())
            ->setResponse($this->application->getResponse())
            ->setRouter($this->serviceManager->get('Router'));

        $this->controller = new $this->controllerFQDN;
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(
            array(
                'router' => array(
                    'routes' => array(
                        $this->controllerRoute => $this->routes[$this->controllerRoute]
                    )
                )
            )
        );
        $this->event->setRouteMatch($this->routeMatch);

        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($this->serviceManager);
    }

    private function loadRoutes()
    {
        $routes = array();
        $invalidModules = array("DoctrineMongoODMModule", "DoctrineModule", "DoctrineORMModule", "DoctrineDataFixtureModule");
        $this->modules = array_diff($moduleManager->getModules(), $invalidModules);

        foreach ($this->modules  as $m) {
            $moduleConfig = include $pathDir.'module/' . ucfirst($m) . '/config/module.config.php';
            if (isset($moduleConfig['router'])) {
                foreach ($moduleConfig['router']['routes'] as $key => $name) {
                    $routes[$key] = $name;
                }
            }
        }

        $this->routes = $routes;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->controller);
        unset($this->request);
        unset($this->routeMatch);
        unset($this->event);
    }
}