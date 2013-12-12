<?php

namespace CafTest\TestCase;

use Doctrine\ORM\Tools\SchemaTool;
use CafTest\AbstractTestCase;

abstract class Service extends AbstractTestCase
{

    /**
     * @var EntityManager
     */
    static $em;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $em = self::$em = self::$serviceManager->get('Doctrine\ORM\EntityManager');

        $schemaTool = new SchemaTool($em);

        $cmf = $em->getMetadataFactory();
        $classes = $cmf->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function getEm()
    {
        return self::$em;
    }

}