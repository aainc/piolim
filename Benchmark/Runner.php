#!/usr/bin/php
<?php
$options = getopt('c:n::h::t::m::');
if (!isset($options['c']) || isset($options['h'])) {?>
Usage:
  not yet
<?php
    exit;
}
$nameSpaces = array('Piolim' => realpath(__DIR__ . '/../'));
spl_autoload_register(function($className) use ($nameSpaces){
    foreach ($nameSpaces as $nameSpace => $path) {
        if (preg_match('#^' . preg_quote($nameSpace) . '#', $className)) {
            $filePath = strtr(preg_replace(
                    '#^' . preg_quote($nameSpace) . '#', $path, $className
                ) , '\\', DIRECTORY_SEPARATOR) . '.php';
            if (file_exists($filePath)) include_once $filePath;
        }
    }
});
$code  = $options['c'];
$name  = isset($options['n']) ? $options['n'] : date('YmdHis');
$times = isset($options['t']) ? $options['t'] : 100;
if (isset($options['m'])) {
    foreach (explode(',', $options['m']) as $row) {
        $result = include_once $row;
        if ($result === false) {
            die('can not include file"' . $row . '"');
        }
    }
}
echo Piolim\Benchmark\TimeThese::run($name, function($i) use ($code) {eval($code);}, $times);

