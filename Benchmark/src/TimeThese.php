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
     * @param int $times benchmark times. default 100
     * @return \Piolim\Benchmark\TimeThese
     */
    public static function run ($name, \Closure $closure, $times = 100) {
        $obj = new self($name);
        for ($i = 0; $i < $times; $i++) {
            $before = microtime(true);
            $result = $closure($i);
            $after  = microtime(true);
            if (!$result) {
            } else if ($result instanceof \Closure) {
                print $result();
            } else {
                print $result . "\n";
            }
            $obj->add($i, $before, $after);
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
            'time' => ($after - $before) * 1000,
            'times' => $times
        );
    }

    /**
     * @return array
     */
    public function calcScores()
    {
        $std = $this->getStandardDeviation();
        $average = $this->getAverage();
        $sorted = $this->logs;
        sort($sorted);
        $contents = array();
        for ($i = 0; $i < count($sorted); $i++) {
            $distance = $std < 1 ? 0 : floor(sqrt(pow(($sorted[$i]['time'] - $average), 2) / $std) +
                (($sorted[$i]['time'] - $average) % $std ? 1 : 0));
            $contents[] = array(
                $i + 1,
                $sorted[$i]['time'],
                $sorted[$i]['time'] - $average,
                $sorted[$i]['times'],
                $distance,
            );
        }
        return $contents;
    }

    /**
     * @return array
     */
    public function summaryDistances()
    {
        $contents = $this->calcScores();
        $length = count($contents);
        $distances = array();
        for($i = 0; $i < $length; $i++) {
           !isset($distances[$contents[$i][4]]) && $distances[$contents[$i][4]] = 0;
           $distances[$contents[$i][4]]++;
        }
        return $distances;
    }

    /**
     * @return array
     */
    public function classifyDistance()
    {
        $maxKey = null;
        if (!$this->summaryDistances()) return array();
        foreach ($this->summaryDistances() as $key => $val) {
            if ($maxKey == null || $maxKey < $key) $maxKey = $key;
        }
        if (!$maxKey) return array();
        $base = $maxKey / 2;
        $classes = array();
        foreach ($this->summaryDistances() as $key => $val) {
            $rank = floor($key / $base);
            if (!isset($classes[$rank])) $classes[$rank] = 0;
            $classes[$rank] += $val;
        }
        $result = array();
        $asc = ord('A');
        $keys = array_keys($classes);
        sort($keys);
        foreach($keys as $key) {
            $from = floor($key * $base);
            $to   = (floor(($key + 1) * $base)) - 1;
            if ($to > $maxKey) $to = $maxKey;
            $result[chr($asc++)] = array(
                'value' => $classes[$key],
                'from' =>  $from,
                'to'   =>  $to,
            );
        }
        return $result;
    }

    /**
     * clear log
     */
    private function clear () {
        $this->logs = array();
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
        return $max;
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
        return $min;
    }

    /**
     * get average time(sec)
     * @return float average
     */
    public function getAverage () {
        return $this->getTotal() / count($this->logs);
    }

    /**
     * get variance of times(sec)
     * @return float variance
     */
    public function getVariance () {
        $ave = $this->getAverage();
        return (float)(array_sum(array_map(function($val) use ($ave){
            return pow($val['time'] - $ave, 2);
        }, $this->logs)) / count($this->logs));
    }

    /**
     * get standard deviation
     * @return float standard deviation
     */
    public function getStandardDeviation () {
        return sqrt($this->getVariance());
    }

    /**
     * get total time(sec)
     * @return number
     */
    public function getTotal () {
        return array_sum(array_map(function($val) {return $val['time'];}, $this->logs));
    }

    /**
     * report
     * @param bool $details
     * @return string
     */
    public function toString ($details = false) {
        $average = $this->getAverage();
        $std = $this->getStandardDeviation();
        $report  = "<< $this->name >>\n";
        $report .= "[abstract]\n";
        $report .= "total:" . $this->getTotal() . " ";
        $report .= "average:" . $average . " ";
        $report .= "min:" . $this->getMin() . " ";
        $report .= "max:" . $this->getMax() . " ";
        $report .= "std:" . $std  . "\n";
        $report .= "\n";
        $report .= "[distances]\n";
        foreach ($this->classifyDistance() as $key => $struct){
            $report .= "$key:" . $struct['value'] . '(' . $struct['from'] . ',' . $struct['to'] . ')' . "\n";
        }
        $report .= "\n";
        foreach ($this->summaryDistances() as $key => $val){
            $report .= "$key = $val\n";
        }
        if ($details) {
            $contents = $this->calcScores();
            array_unshift($contents,array('rank', 'time', 'division', 'number', 'distance'));
            $length = array();
            foreach ($contents as $content) {
                for ($i = 0; $i < count($content); $i++) {
                    if (!isset($length[$i]) || $length[$i] < strlen($content[$i])) {
                        $length[$i] = strlen($content[$i]);
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
        }
        return $report;
    }

    public function __toString()
    {
        return $this->toString(true);
    }

}
