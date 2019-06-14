<?php

namespace BYanelli\Memoizer;

class MemoizationHasher
{
    static $instance = null;

    public static function getInstance()
    {
        return static::$instance ?: (static::$instance = new static);
    }

    private function isSequentialArray($arr): bool
    {
        return (array_keys($arr) === range(0, count($arr) - 1));
    }

    private function isDictionary(array $arr): bool
    {
        return !$this->isSequentialArray($arr);
    }

    public function hash($value): string
    {
        if (
            is_string($value)
            || is_int($value)
            || is_float($value)
            || is_bool($value)
            || is_null($value)
        ) {
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
    }
}