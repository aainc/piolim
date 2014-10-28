<?php
namespace Piolim\Benchmark\phpunit;
use Piolim\Annotation;
use Piolim\Benchmark\TimeThese;

class TimeTheseTest implements \PHPUnit_Framework_Test
{
    private $annotation;
    private $closure = null;

    public function __construct(\Closure $closure, Annotation $annotation )
    {
        $this->closure = $closure;
        $this->annotation = $annotation;
    }

    public function count()
    {
        return 1;
    }

    /**
     * @param \PHPUnit_Framework_TestResult $result
     * @return \PHPUnit_Framework_TestResult
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        if ($result === null) {
            $result = new \PHPUnit_Framework_TestResult;
        }
        $result->startTest($this);
        \PHP_Timer::start();
        $times = null;
        if ($this->annotation['times'] !== null) $times = $this->annotation['times'];
        $timeThese = TimeThese::run($this->annotation->getName(), $this->closure, $times);
        $stopTime = null;
        try {
            if ($this->annotation['avg'] !== null ) {
                \PHPUnit_Framework_Assert::assertThat(
                    $timeThese->getAverage(),
                    new \PHPUnit_Framework_Constraint_LessThan($this->annotation['avg']),
                    'Average:' . $this->annotation['avg'] . 'msec'
                    );
            }
            if ($this->annotation['total'] !== null)  {
                \PHPUnit_Framework_Assert::assertThat(
                    $timeThese->getTotal(),
                    new \PHPUnit_Framework_Constraint_LessThan($this->annotation['total']),
                    'Total:' . $this->annotation['total'] . 'msec'
                );
            }
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            $stopTime = \PHP_Timer::stop();
            $result->addFailure($this, $e, $stopTime);
        } catch (\Exception $e) {
            $stopTime = \PHP_Timer::stop();
            $result->addError($this, $e, $stopTime);
        }

        if ($stopTime === null) {
            $stopTime = \PHP_Timer::stop();
        }
        $result->endTest($this, $stopTime);
        return $result;
    }
}
