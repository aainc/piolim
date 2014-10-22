<?php
namespace Piolim\Benchmark;

class TimeTheseTest extends \PHPUnit_Framework_TestCase{
    /**
     * @var \Piolim\Benchmark\TimeThese
     */
    private $target = null;
    public function setUp () {
        $this->target = new TimeThese("test");
        $this->target->add(1, 1, 2);
        $this->target->add(1, 1, 3);
        $this->target->add(1, 1, 4);
    }

    public function getTotal () {
        $this->assertEquals(6000, $this->target->getTotal());
    }
    public function testMax () {
        $this->assertEquals(3000, $this->target->getMax());
    }

    public function testMin () {
        $this->assertEquals(1000, $this->target->getMin());
    }

    public function testAverage () {
        $this->assertEquals(2000, $this->target->getAverage());
    }

    public function testVariance () {
        $this->assertEquals(666, floor($this->target->getVariance()));
    }

    public function testStandardDeviation () {
        $this->assertEquals(25.0, floor($this->target->getStandardDeviation()));
    }
}