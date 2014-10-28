<?php
namespace Piolim\Benchmark\phpunit;
class TimeTheseTesterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TimeTheseTester
     */
    private $target = null;
    private $suite = null;

    public function setUp () {
        $this->target = new TimeTheseTester();
        $this->suite = \Phake::mock('\PHPUnit_Framework_TestSuite');
    }

    public function testStartTestSuite () {
        \Phake::when($this->suite)->getName()->thenReturn('Piolim\Benchmark\phpunit\TimeTheseTesterTest');
        $this->target->startTestSuite($this->suite);
        \Phake::verify($this->suite)->addTest($this->isInstanceOf('\Piolim\Benchmark\phpunit\TimeTheseTest'));
    }

    public function testStartTestSuiteNoTests () {
        \Phake::when($this->suite)->getName()->thenReturn('Piolim\Benchmark\phpunit\TimeTheseTestTest');
        $this->target->startTestSuite($this->suite);
        \Phake::verify($this->suite, \Phake::never())->addTest($this->isInstanceOf('\Piolim\Benchmark\phpunit\TimeTheseTest'));
    }
    /**
     * @Bench
     */
    public function benchStartTestSuite() {
    }
}
