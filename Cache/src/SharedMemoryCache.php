<?php
namespace Piolim\Cache;
class SharedMemoryCache implements CacheManager
{
    private $id = null;
    private $data = null;
    const MAX_SIZE = 1048576;
    public function __construct()
    {
        $identifier = ftok($_SERVER['PHP_SELF'], 'r');
        $this->id = @shmop_open($identifier, 'w', 0666, self::MAX_SIZE);
        if (!$this->id) {
            $this->id = @shmop_open($identifier, 'c', 0666, self::MAX_SIZE);
        }
        $size = shmop_size($this->id);
        if ($size) {
            $data = shmop_read($this->id, 0, $size);
            $this->data = @unserialize($data);
        }
        if (!$this->data) {
            $this->data = array();
        }

    }

    public function register($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function delete($key)
    {
        if (isset($this->data[$key])) unset($this->data[$key]);
    }

    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    public function count()
    {
        return count($this->data);
    }

    public function flush()
    {
        $string = serialize($this->data);
        if (strlen($string) > self::MAX_SIZE) {
            $string = serialize(array());
            shmop_delete($this->id);
        }
        shmop_write($this->id, $string, 0);
        shmop_close($this->id);
        $this->id = null;
    }

    public function __destruct()
    {
        if ($this->id !== null) {
            $this->flush();
        }
    }
}
