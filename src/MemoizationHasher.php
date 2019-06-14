<?php

namespace BYanelli\Memoizer;

class MemoizationHasher
{
    static $instance = null;

    public static function getInstance()
    {
        return static::$instance ?: (static::$instance = new static);
    }

    private function isScalar($value): bool
    {
        return (
            is_string($value)
            || is_int($value)
            || is_float($value)
            || is_bool($value)
            || is_null($value)
        );
    }

    private function isSequentialArray($arr): bool
    {
        return is_array($arr) && (array_keys($arr) === range(0, count($arr) - 1));
    }

    private function isDictionary($arr): bool
    {
        return is_array($arr) && !$this->isSequentialArray($arr);
    }

    private function isJsonable($object): bool
    {
        return method_exists($object, 'toJson');
    }

    private function isArrayable($object): bool
    {
        return method_exists($object, 'toArray');
    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    public function hash($value): string
    {
        if ($this->isScalar($value)) {
            return md5(gettype($value).':'.strval($value));
        }

        elseif ($this->isSequentialArray($value)) {
            return md5(array_reduce($value, function (string $result, $next) {return $result.$this->hash($next);}, ''));
        }

        elseif ($this->isDictionary($value)) {
            $value = array_map(function ($key, $item) {return $this->hash($key).$this->hash($item);}, array_keys($value), $value);

            sort($value);

            return md5(implode('', $value));
        }

        elseif (is_object($object = $value)) {
            if ($this->isJsonable($object)) {
                return md5($object->toJson());
            }

            elseif ($this->isArrayable($object)) {
                return $this->hash($object->toArray());
            }

            else {
                return md5(serialize($object));
            }
        }

        throw new \Exception;
    }
}