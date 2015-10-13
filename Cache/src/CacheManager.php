<?php

/**
 * Date: 15/09/24
 * Time: 20:05.
 */
namespace Piolim\Cache;

interface CacheManager
{
    public function register($key, $value);
    public function delete($key);
    public function get($key);
}
