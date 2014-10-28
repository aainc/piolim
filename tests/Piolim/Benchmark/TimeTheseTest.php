<?php
namespace Piolim\Benchmark;

class TimeTheseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Piolim\Benchmark\TimeThese
     */
    private $target = null;
    public function setUp () {
        $this->target = new TimeThese("test");
        $this->target->add(1, 1, 2);
        $this->target->add(2, 1, 3);
        $this->target->add(3, 1, 4);
    }

    public function testTotal () {
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
        $this->assertEquals(666666, floor($this->target->getVariance()));
    }

    public function testStandardDeviation () {
        $this->assertEquals(816.0, floor($this->target->getStandardDeviation()));
    }

    public function testCalcScores () {
        $contents = $this->target->calcScores();
        $this->assertEquals(1,$contents[0][0]);
        $this->assertEquals(2,$contents[1][0]);
        $this->assertEquals(3,$contents[2][0]);
        $this->assertEquals(1000,$contents[0][1]);
        $this->assertEquals(2000,$contents[1][1]);
        $this->assertEquals(3000,$contents[2][1]);
        $this->assertEquals(-1000,$contents[0][2]);
        $this->assertEquals(0,$contents[1][2]);
        $this->assertEquals(1000,$contents[2][2]);
        $this->assertEquals(1,$contents[0][3]);
        $this->assertEquals(2,$contents[1][3]);
        $this->assertEquals(3,$contents[2][3]);
        $this->assertEquals(35,$contents[0][4]);
        $this->assertEquals(0,$contents[1][4]);
        $this->assertEquals(35,$contents[2][4]);
    }
}
