<?php
namespace Piolim\Benchmark\phpunit;

class TimeTheseTestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TimeTheseTest
     */
    private $target = null;
    private $annotation = null;
    private $result = null;

    public function setUp () {
        $this->annotation = \Phake::mock('\Piolim\Annotation');
        $this->target = new TimeTheseTest(function($i){
            sleep(1);
        }, $this->annotation);
    }

    public function testSuccess () {
        \Phake::when($this->annotation)->offsetGet('times')->thenReturn(1);
        \Phake::when($this->annotation)->offsetGet('avg')->thenReturn(1500);
        \Phake::when($this->annotation)->offsetGet('total')->thenReturn(1500);
        $result = $this->target->run($this->result);
        $this->assertEquals(0, $result->failureCount());
    }

    public function testFail () {
        \Phake::when($this->annotation)->offsetGet('times')->thenReturn(1);
        \Phake::when($this->annotation)->offsetGet('avg')->thenReturn(900);
        \Phake::when($this->annotation)->offsetGet('total')->thenReturn(900);
        $result = $this->target->run();
        $this->assertEquals(1, $result->failureCount());
    }
}
