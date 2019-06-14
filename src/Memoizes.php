<?php

namespace BYanelli\Memoizer;

trait Memoizes
{
    protected static function _memoize($classOrInstance, array $dependencies, callable $callable)
    {
        $hasher = MemoizationHasher::getInstance();
        $cache = MemoizationCache::getInstance();

        $hash = $hasher->hash($dependencies);

        $method = (
            collect(debug_backtrace())
                ->whereNotIn('function', ['memoize', '_memoize'])
                ->pluck('function')
                ->first()
        );

        if (!isset($method)) {
            throw new \Exception;
        }

        return $cache->remember($classOrInstance, $method, $hash, $callable);
    }

    /**
     * @param array|callable $dependenciesOrCallable
     * @param callable|null $callable
     * @return mixed
     * @throws \Exception
     */
    protected function memoize($dependenciesOrCallable, $callable=null)
    {
        if ($callable === null) {
            $dependencies = [];
            $callable = $dependenciesOrCallable;
        } else {
            $dependencies = $dependenciesOrCallable;
        }

        return static::_memoize($this, $dependencies, $callable);
    }
}