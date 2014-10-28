<?php
namespace Piolim\Benchmark\phpunit;
use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestSuite;

class TimeTheseTester implements \PHPUnit_Framework_TestListener
{
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $testCase = $suite->getName();
        if (class_exists($suite->getName())) {
            $clazz = new \ReflectionClass($testCase);
            if ($clazz->isSubclassOf('PHPUnit_Framework_TestCase')) {
                foreach ($clazz->getMethods() as $method) {
                    $annotations = \Piolim\Annotations::analyze($method->getDocComment());
                    if (!$annotations->exists('Bench')) continue;
                    $annotation = $annotations->getByName('Bench');
                    $suite->addTest(new TimeTheseTest(function($i) use($method, $suite) {
                        $method->invoke($suite);
                    },$annotation));
                }
            }
        }
    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {

    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
    }
}
