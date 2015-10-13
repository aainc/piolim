<?php
namespace Piolim\Benchmark;

class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    public function testAnalyze () {
        $testData = '
/**
 * @anno1
      *      @anno2     (prop1="value1", prop2    = 2, prop3 = "test\"test")
 */';
        $result = Annotations::analyze($testData);
        $this->assertSame('anno1', $result[0]->getName());
        $this->assertSame(array(), $result[0]->getProperties());
        $this->assertSame('anno2', $result[1]->getName());
        $this->assertSame('value1', $result[1]->getProperty('prop1'));
        $this->assertEquals(2, $result[1]->getProperty('prop2'));
        $this->assertSame('test"test', $result[1]->getProperty('prop3'));
    }
}
