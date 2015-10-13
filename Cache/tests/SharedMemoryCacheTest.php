<?php
/**
 * Date: 15/10/13
 * Time: 20:57
 */

namespace Piolim\Cache;


class SharedMemoryCacheTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    public function  setup ()
    {
        $this->target = new SharedMemoryCache();
        $this->target->register('hoge', 100);
    }

    public function testRegister ()
    {
        $this->target->register('hoge', 200);
        $this->assertSame(1, $this->target->count());
        $this->assertSame(200, $this->target->get('hoge'));
    }

    public function testDelete ()
    {
        $this->target->delete('hoge');
        $this->assertSame(0, $this->target->count());
        $this->assertSame(null, $this->target->get('hoge'));
    }

    public function testGet ()
    {
        $this->assertSame(100, $this->target->get('hoge'));
    }

    public function testGetDefault ()
    {
        $this->assertSame(300, $this->target->get('fuga', 300));
    }

    public function testCount ()
    {
        $this->assertSame(1, $this->target->count());
    }
}
