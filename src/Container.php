<?php

namespace Humble\Container;

class Container implements \ArrayAccess, \Interop\Container\ContainerInterface
{
    private $callables;
    private $values;

    public function offsetExists($offset)
    {
        return isset($this->callables[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new NotFoundException(sprintf('key [%s] not found', $offset));
        }

        if (!isset($this->values[$offset])) {
            $this->values[$offset] = $this->callables[$offset]($this);
        }

        return $this->values[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            throw new ContainerException(sprintf('key [%s] already set', $offset));
        }

        if (!is_callable($value)) {
            throw new ContainerException(sprintf('value of [%s] must be callable', $offset));
        }

        $this->callables[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->callables[$offset]);
        unset($this->values[$offset]);
    }

    public function has($id)
    {
        return $this->offsetExists($id);
    }

    public function get($id)
    {
        return $this->offsetGet($id);
    }

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }
}
