<?php

namespace BYanelli\Memoizer;

class MemoizationHasher
{
    static $instance = null;

    public static function getInstance()
    {
        return static::$instance ?: (static::$instance = new static);
    }

    public function hash($value, $key=null): string
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
    }
}