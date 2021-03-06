<?php

namespace Perform\BaseBundle\Tests\DataCollector;

use PHPUnit\Framework\TestCase;
use Perform\BaseBundle\Crud\CrudRegistry;
use Perform\BaseBundle\DataCollector\CrudDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Tests\Crud\TestCrud;
use Perform\BaseBundle\Tests\Crud\TestEntity;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudDataCollectorTest extends TestCase
{
    public function setUp()
    {
        $this->registry = $this->getMockBuilder(CrudRegistry::class)
                        ->disableOriginalConstructor()
                        ->getMock();
        $store = $this->createMock(ConfigStoreInterface::class);
        $this->collector = new CrudDataCollector($this->registry, $store, []);
    }

    public function testCollectGetsLoadedCrud()
    {
        $this->registry->expects($this->any())
            ->method('all')
            ->will($this->returnValue([
                'test' => new TestCrud(),
            ]));

        $this->collector->collect(new Request(), new Response());
        $expected = [
            'test' => [
                TestCrud::class,
                TestEntity::class,
            ],
        ];
        $this->assertEquals($expected, $this->collector->getCrudNames());
    }
}
