<?php namespace Cobwebinfo\ShrekApiClient\Tests;

class ApcStoreTest extends \PHPUnit_Framework_TestCase
{
    public function testGetReturnsNullWhenNotFound()
    {
        $apc = $this->getMockBuilder('\Cobwebinfo\ShrekApiClient\Cache\ApcWrapper')->setMethods(['get'])->getMock();
        $apc->expects($this->once())->method('get')->with($this->equalTo('foobar'))->will($this->returnValue(null));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\ApcStore($apc, 'foo');
        $this->assertNull($store->get('bar'));
    }

    public function testAPCValueIsReturned()
    {
        $apc = $this->getMockBuilder('\Cobwebinfo\ShrekApiClient\Cache\ApcWrapper')->setMethods(['get'])->getMock();
        $apc->expects($this->once())->method('get')->will($this->returnValue('bar'));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\ApcStore($apc);
        $this->assertEquals('bar', $store->get('foo'));
    }

    public function testGetMultipleReturnsNullWhenNotFoundAndValueWhenFound()
    {
        $apc = $this->getMockBuilder('\Cobwebinfo\ShrekApiClient\Cache\ApcWrapper')->setMethods(['get'])->getMock();
        $apc->expects($this->exactly(3))->method('get')->willReturnMap([
            ['foo', 'qux'],
            ['bar', null],
            ['baz', 'norf'],
        ]);
        $store = new \Cobwebinfo\ShrekApiClient\Cache\ApcStore($apc);
        $this->assertEquals([
            'foo'   => 'qux',
            'bar'   => null,
            'baz'   => 'norf',
        ], $store->many(['foo', 'bar', 'baz']));
    }

    public function testSetMethodProperlyCallsAPC()
    {
        $apc = $this->getMockBuilder('\Cobwebinfo\ShrekApiClient\Cache\ApcWrapper')->setMethods(['put'])->getMock();
        $apc->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(60));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\ApcStore($apc);
        $store->put('foo', 'bar', 1);
    }

    public function testSetMultipleMethodProperlyCallsAPC()
    {
        $apc = $this->getMockBuilder('\Cobwebinfo\ShrekApiClient\Cache\ApcWrapper')->setMethods(['put'])->getMock();
        $apc->expects($this->exactly(3))->method('put')->withConsecutive([
            $this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(60),
        ], [
            $this->equalTo('baz'), $this->equalTo('qux'), $this->equalTo(60),
        ], [
            $this->equalTo('bar'), $this->equalTo('norf'), $this->equalTo(60),
        ]);
        $store = new \Cobwebinfo\ShrekApiClient\Cache\ApcStore($apc);
        $store->putMany([
            'foo'   => 'bar',
            'baz'   => 'qux',
            'bar'   => 'norf',
        ], 1);
    }

    public function testStoreItemForeverProperlyCallsAPC()
    {
        $apc = $this->getMockBuilder('\Cobwebinfo\ShrekApiClient\Cache\ApcWrapper')->setMethods(['put'])->getMock();
        $apc->expects($this->once())->method('put')->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo(0));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\ApcStore($apc);
        $store->forever('foo', 'bar');
    }

    public function testForgetMethodProperlyCallsAPC()
    {
        $apc = $this->getMockBuilder('\Cobwebinfo\ShrekApiClient\Cache\ApcWrapper')->setMethods(['delete'])->getMock();
        $apc->expects($this->once())->method('delete')->with($this->equalTo('foo'));
        $store = new \Cobwebinfo\ShrekApiClient\Cache\ApcStore($apc);
        $store->forget('foo');
    }
}