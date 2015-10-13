<?php
namespace Piolim\Benchmark;
class Annotations implements \ArrayAccess {
    /**
     * @param $comment
     * @return Annotations
     */
    public static function analyze ($comment) {
        $result = new self();
        if (preg_match_all('#(@.+)$#m', $comment, $matches)) {
            $annotations = $matches[1];
            $length = count($annotations);
            for ($i = 0; $i < $length; $i++) {
                $annotation = new Annotation($annotations[$i]);
                $className = __NAMESPACE__ . '\\'. $annotation->getName();
                if (class_exists($className)) {
                    $obj = new $className();
                    if ($obj instanceof Annotation) {
                        $obj->copy($annotation);
                        $annotation = $obj;
                    }
                }
                $result[] = $annotation;
            }
        }
        return $result;
    }

    private $elements = array();
    public function offsetExists ($offset) {
        return isset($this->elements[$offset]);
    }

    public function offsetGet ($offset) {
        return isset($this->elements[$offset]) ? $this->elements[$offset] : null;
    }

    public function offsetSet ($offset, $value) {
        if (!($value instanceof Annotation)) {
            throw new \IllegalArgumentException();
        }

        if (is_null($offset)) {
            $this->elements[] = $value;
        }
        else {
            $this->elements[$offset] = $value;
        }
    }

    public function offsetUnset ($offset){
        unset($this->elements[$offset]);
    }

    public function exists ($name) {
        $filtered = array_filter($this->elements, function($element) use ($name){
            return $element->getName() === $name;
        });
        return count($filtered) > 0;
    }

    /**
     * @param $name
     * @return Annotation
     * @throws \IllegalArgumentException
     */
    public function getByName ($name) {
        $filtered = array_filter($this->elements, function($element) use ($name){
            return $element->getName() === $name;
        });
        if (!count($filtered)) {
            throw new \IllegalArgumentException();
        }
        return $filtered[0];
    }
}
