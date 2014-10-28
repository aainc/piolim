<?php
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

