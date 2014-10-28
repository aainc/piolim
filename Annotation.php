<?php
namespace Piolim;
class Annotation implements \ArrayAccess {
    private $name = null;
    private $properties = null;

    public function __construct ($annotation = null) {
        if ($annotation !== null) {
            $this->analyze($annotation);
        }
    }

    public function getName () {
        return $this->name;
    }

    public function getProperties () {
        return $this->properties;
    }

    public function getProperty ($name) {
        $result = null;
        if (!isset($this->properties[$name])) {
            $result = null;
        } else {
            $result = $this->properties[$name];
            $definitions = $this->getDefinitions();
            if ($definitions && !in_array($name, $definitions)) {
                $result = null;
            }
        }
        return $result;
    }

    public function analyze ($annotation) {
        $result = array();
        $length = mb_strlen($annotation);
        $i = 0;
        $name = '';
        while ($i++ < $length) {
            $char = mb_substr($annotation, $i, 1);
            if ($char === ' ') continue;
            if ($char === '(' || $char === "\n") break;
            $name .= $char;
        }

        $quote = '';
        $property = '';
        while ($i++ < $length) {
            $char = mb_substr($annotation, $i, 1);
            $property .= $char;
            if ($quote !== '' ) {
                if ($char === $quote) {
                    $quote = '';
                } elseif ($char === '\\') {
                    $property .= mb_substr($annotation, ++$i, 1);
                }
            } else {
                if ($char === '"' || $char === "'") {
                    $quote = $char;
                }
                elseif ($quote === '' && ($char === ')' || $char === "\n")) {
                    $property = preg_replace('#\)|\n$#', '', $property);
                    break;
                }
            }
        }
        $this->name = $name;
        $this->properties = $this->analyzeProperty($property);
    }

    public function analyzeProperty ($property) {
        $length = mb_strlen($property);
        $elements = array();
        $buf = '';
        $quote = '';
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($property, $i, 1);
            if ($quote !== '' ) {
                for (;$i < $length; $i++) {
                    $char = mb_substr($property, $i, 1);
                    $buf .= $char;
                    if ($char === '\\') {
                        $buf .= mb_substr($property, $i++, 1);
                    } elseif ($char === $quote) {
                        $quote = '';
                        break;
                    }
                }
            } else {
                if ($char === ',') {
                    $elements[] = $buf;
                    $buf = '';
                } else {
                    $buf .= $char;
                }
            }
        }
        if ($buf)  {
            $elements[] = $buf;
        }
        $result = array();
        foreach ($elements as $element) {
            list ($key, $value) = self::splitKeyValues ($element);
            $result[$key] = $value;
        }
        return $result;
    }

    public function splitKeyValues ($element) {
        $length = mb_strlen($element);
        $key    = '';
        $i = 0;
        for ( ; $i < $length; $i++) {
            $char = mb_substr($element, $i, 1);
            if ($char === ' ') continue;
            if ($char === '=') break;
            $key .= $char;
        }

        $value  = '';
        for ($i++; $i < $length; $i++) {
            $char = mb_substr($element, $i, 1);
            if ($char === '"' || $char === "'")  {
                $quote = $char;
                for ($i++; $i < $length; $i++) {
                    $char = mb_substr($element, $i, 1);
                    if ($char === '\\') {
                        $value .= mb_substr($element, ++$i, 1);
                    } elseif ($char === $quote) {
                        $quote = '';
                        break;
                    } else {
                        $value .= $char;
                    }
                }
            }
            else {
                if ($char === ' ') continue;
                $value .= $char;
            }
        }
        return array ($key, $value);
    }

    public function offsetExists ($offset) {
        return isset($this->properties[$offset]);
    }

    public function offsetGet ($offset) {
        return $this->getProperty($offset);
    }

    public function offsetSet ($offset, $value) {
        if (is_null($offset)) {
            $this->properties[] = $value;
        }
        else {
            $this->properties[$offset] = $value;
        }
    }

    public function offsetUnset ($offset){
        unset($this->properties[$offset]);
    }

    public function getDefinitions () {
        return array();
    }

    public function copy (Annotation $annotation) {
        $this->name = $annotation->getName();
        $this->properties = $annotation->getProperties();
    }
}
