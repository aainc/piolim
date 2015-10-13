#!/usr/bin/php
<?php
$options = getopt('c:n::h::t::m::u::');
if ((!isset($options['c']) && !isset($options['u'])) || isset($options['h'])) {?>
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

$runner = null;
if (isset($options['u'])) {
    $url = $options['u'];
    $obj = curl_init($url);
    curl_setopt($obj, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($obj, CURLOPT_SSL_VERIFYPEER, false); // 証明書の検証を行わない
    curl_setopt($obj, CURLOPT_RETURNTRANSFER, true);  // curl_execの結果を文字列で返す
    $runner = function($i) use($obj) {
        curl_exec($obj);
    };
} else {
    $code  = $options['c'];
    $runner = function($i) use($code) {
        return eval($code);
    };
}

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
echo Piolim\Benchmark\TimeThese::run($name, $runner, $times);

