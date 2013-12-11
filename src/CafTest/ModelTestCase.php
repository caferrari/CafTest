<?php

namespace CafTest;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Mvc\MvcEvent;

class ModelTestCase extends AbstractTestCase
{

    protected $service = 'Doctrine\ORM\EntityManager';

    /**
     * @var EntityManager
     */
    protected $em;

    public function tearDown()
    {
        parent::tearDown();
    }

    public function getEm()
    {
        if (isset($this->em)) {
            return $this->em;

        }
        return $this->em = $this->application->getServiceManager()->get($this->service);
    }

    public function getEmMock()
    {
        $emMock = $this->getMock(
            '\Doctrine\ORM\EntityManager',
            array('getRepository', 'getClassMetadata', 'persist', 'flush'), array(), '', false
        );

        $emMock->expects($this->any())
            ->method('persist')
            ->will(
                $this->returnCallback(
                    function ($entity) {
                        if (method_exists($entity, 'prePersist')) {
                            $entity->prePersist();
                        }
                        return $entity;
                    }
                )
            );

        $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));

        return $emMock;
    }
}