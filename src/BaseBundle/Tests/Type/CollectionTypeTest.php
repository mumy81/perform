<?php

namespace Perform\BaseBundle\Tests\Type;

use Perform\BaseBundle\Entity\User;
use Perform\BaseBundle\Type\CollectionType;
use Doctrine\Common\Collections\ArrayCollection;
use Perform\BaseBundle\Admin\AdminRegistry;

/**
 * CollectionTypeTest
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CollectionTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $type;

    public function setUp()
    {
        $registry = $this->getMockBuilder(AdminRegistry::class)
                  ->disableOriginalConstructor()
                  ->getMock();
        $this->type = new CollectionType($registry);
    }

    public function testListContextDefaultItemLabel()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection([1]);
        $this->assertSame('1 item', $this->type->listContext($entity, 'items'));

        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertSame('4 items', $this->type->listContext($entity, 'items'));
    }

    public function testListContextWithItemLabel()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection([1]);
        $this->assertSame('1 thing', $this->type->listContext($entity, 'items', ['itemLabel' => 'thing']));

        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertSame('4 things', $this->type->listContext($entity, 'items', ['itemLabel' => 'thing']));
    }

    public function testListContextWithItemLabelPlural()
    {
        $entity = new \stdClass();
        $entity->items = new ArrayCollection([1]);
        $this->assertSame('1 thing', $this->type->listContext($entity, 'items', ['itemLabel' => ['thing', 'things (a lot)']]));

        $entity->items = new ArrayCollection([1, 2, 3, 4]);
        $this->assertSame('4 things (a lot)', $this->type->listContext($entity, 'items', ['itemLabel' => ['thing', 'things (a lot)']]));
    }
}
