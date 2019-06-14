<?php

namespace BYanelli\Memoizer;

class MemoizationCache
{
    static $instance = null;

    public static function getInstance()
    {
        return static::$instance ?: (static::$instance = new static);
    }

    private function ensureInstanceHasMemoizationCache($instance)
    {
        if (!isset($instance->___memoizationCache)) {
            $instance->___memoizationCache = [];
        }
    }

    private function existsInInstance($instance, string $method, string $hash): bool
    {
        $this->ensureInstanceHasMemoizationCache($instance);

        return isset($instance->___memoizationCache[$method][$hash]);
    }

    public function has($class, string $method, string $hash): bool
    {
        if (is_object($class)) {
            return $this->existsInInstance($class, $method, $hash);
        } else {
            throw new \Exception;
        }
    }

    private function getFromInstance($instance, string $method, string $hash, $default)
    {
        $this->ensureInstanceHasMemoizationCache($instance);

        return $instance->___memoizationCache[$method][$hash] ?? $default;
    }

    public function get($class, string $method, string $hash, $default=null)
    {
        if (is_object($class)) {
            return $this->getFromInstance($class, $method, $hash, $default);
        } else {
            throw new \Exception;
        }
    }

    private function putIntoInstance($instance, string $method, string $hash, $value)
    {
        $this->ensureInstanceHasMemoizationCache($instance);

        $instance->___memoizationCache[$method][$hash] = $value;
    }

    private function put($class, string $method, string $hash, $value): void
    {
        if (is_object($class)) {
            $this->putIntoInstance($class, $method, $hash, $value);
        } else {
            throw new \Exception;
        }
    }

    public function remember($class, string $method, string $hash, callable $callable)
    {
        if ($this->has($class, $method, $hash)) {
            return $this->get($class, $method, $hash);
        } else {
            $result = $callable();

            $this->put($class, $method, $hash, $result);

            return $result;
        }
    }
}