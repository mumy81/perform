<?php

namespace Perform\BaseBundle\Tests\Crud;

use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Config\TypeConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CrudRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testGetContext()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_VIEW);
        $this->assertSame(TypeConfig::CONTEXT_VIEW, $req->getContext());
    }

    public function testGetSetEntityClass()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_LIST);
        $this->assertSame($req, $req->setEntityClass('FooBundle:Foo'));
        $this->assertSame('FooBundle:Foo', $req->getEntityClass());
    }

    public function testGetSetPage()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_LIST);
        $this->assertSame($req, $req->setPage(2));
        $this->assertSame(2, $req->getPage());
    }

    public function testGetDefaultPage()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_LIST);
        $this->assertSame(1, $req->getPage());
    }

    public function testGetSetSortField()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_LIST);
        $this->assertSame($req, $req->setSortField('title'));
        $this->assertSame('title', $req->getSortField());
    }

    public function testGetSetSortDirection()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_LIST);
        $this->assertSame($req, $req->setSortDirection('desc'));
        $this->assertSame('DESC', $req->getSortDirection());

        $this->assertSame($req, $req->setSortDirection('n'));
        $this->assertSame('N', $req->getSortDirection());
    }

    public function testGetDefaultSortDirection()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_LIST);
        $this->assertSame('ASC', $req->getSortDirection());
    }

    public function testBadSortDirection()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_LIST);
        $this->assertSame($req, $req->setSortDirection('foo'));
        $this->assertSame('ASC', $req->getSortDirection());
    }

    public function testGetSetFilter()
    {
        $req = new CrudRequest(TypeConfig::CONTEXT_LIST);
        $this->assertSame($req, $req->setFilter('new'));
        $this->assertSame('new', $req->getFilter());
    }

    public function testFromRequest()
    {
        $request = new Request([
            'page' => 2,
            'sort' => 'title',
            'direction' => 'DESC',
            'filter' => 'some_filter',
        ]);
        $request->attributes->set('_entity', 'FooBundle:Foo');
        $req = CrudRequest::fromRequest($request, TypeConfig::CONTEXT_LIST);

        $this->assertSame(TypeConfig::CONTEXT_LIST, $req->getContext());
        $this->assertSame('FooBundle:Foo', $req->getEntityClass());
        $this->assertSame(2, $req->getPage());
        $this->assertSame('title', $req->getSortField());
        $this->assertSame('DESC', $req->getSortDirection());
        $this->assertSame('some_filter', $req->getFilter());
    }
}