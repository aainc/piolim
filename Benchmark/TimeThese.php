<?php
namespace Piolim\Benchmark;
/**
 * Easy benchmark for PHP Script
 * Class TimeThese
 * @package Time
 */
class TimeThese {
    /**
     * calculate benchmark
     * @param $name string Test name
     * @param callable $closure benchmark target
     * @param int $times benchmark times. default 0
     * @return \Piolim\Benchmark\TimeThese
     */
    public static function run ($name, \Closure $closure, $times = 100) {
        $obj = new self($name);
        for ($i = 0; $i < $times; $i++) {
            $before = microtime(true);
            $result = $closure($i);
            if ($result) {
                print $result . "\n";
            }
            $obj->add($i, $before, microtime(true));
        }
        return $obj;
    }

    private $name = null;
    private $logs = array();

    public function __construct ($name) {
        $this->name = $name;
    }

    /**
     * add process log
     * @param $times int log number
     * @param $before float before unixtime(msec)
     * @param $after float after unixtime (msec)
     */
    public function add ($times, $before, $after) {
        $this->logs[] = array(
            'time' => $after - $before,
            'times' => $times
        );
    }

    /**
     * clear log
     */
    private function clear () {
        $this->logs[] = array();
    }

    /**
     * get maximum time(sec)
     * @return int
     */
    public function getMax () {
        $max = 0;
        foreach ($this->logs as $val) {
            if (!$max || $max < $val['time']) {
                $max = $val['time'];
            }
        }
        return $max * 1000;
    }

    /**
     * get minimum time(sec)
     * @return int
     */
    public function getMin () {
        $min = 0;
        foreach ($this->logs as $val) {
            if (!$min || $min > $val['time']) {
                $min = $val['time'];
            }
        }
        return $min * 1000;
    }

    /**
     * get average time
     * @return float average
     */
    public function getAverage () {
        return ($this->getTotal() / count($this->logs));
    }

    /**
     * get variance of times
     * @return float variance
     */
    public function getVariance () {
        $ave = $this->getAverage();
        return (float)(array_sum(array_map(function($val) use ($ave){
            return pow($val['time'] - $ave / 1000, 2);
        },$this->logs)) / count($this->logs)) * 1000;
    }

    /**
     * get standard deviation
     * @return float standard deviation
     */
    public function getStandardDeviation () {
        return sqrt($this->getVariance());
    }

    /**
     * @return number
     */
    public function getTotal () {
        return array_sum(array_map(function($val) {return $val['time'];}, $this->logs)) * 1000;
    }

    /**
     * report
     */
    public function toString ($details = false) {
        $average = $this->getAverage();
        $std = $this->getStandardDeviation();
        $report  = "<< $this->name >>\n";
        $report .= "total:" . $this->getTotal() . " ";
        $report .= "average:" . $average . " ";
        $report .= "min:" . $this->getMin() . " ";
        $report .= "max:" . $this->getMax() . " ";
        $report .= "std:" . $std  . "\n";
        if ($details)  {
            $sorted = $this->logs;
            sort($sorted);
            $contents   = array();
            $contents[] = array('rank', 'time', 'division', 'number', 'distance');
            $distances = array();
            for ($i = 0; $i < count($sorted); $i++) {
                $distance = floor(sqrt(pow(($sorted[$i]['time'] * 1000 - $average), 2) / $std) +
                    (($sorted[$i]['time'] * 1000 - $average) % $std ? 1 : 0));
                $contents[] = array(
                    $i + 1,
                    $sorted[$i]['time'] * 1000,
                    ($sorted[$i]['time'] * 1001 - $average),
                    $sorted[$i]['times'],
                    $distance,
                );
                $distances[$distance]++;
            }
            $length = array();
            foreach ($contents as $content) {
                for ($i = 0; $i < count($content); $i++) {
                    if ($length[$i] < strlen($content[$i])) {
                        $length[$i]= strlen($content[$i]);
                    }
                }
            }

            $row = 0;
            $report .= str_repeat('_', array_sum($length) + 1 + count($length) * 3) . "\n";
            foreach ($contents as $content) {
                $report .= '| ';
                for ($i = 0; $i < count($content); $i++) {
                    $report .= str_pad($content[$i], $length[$i], ' ', $row ? STR_PAD_LEFT : STR_PAD_BOTH);
                    $report .= ' | ';
                }
                $report .= "\n";
                $row++;
            }
            $report .= "<<distances>>\n";
            foreach ($distances as $key => $val) {
                if (!$val) continue;
                $report .= "$key = $val\n";
            }
        }
        return $report;
    }

    public function __toString()
    {
        return $this->toString(true);
    }

}
