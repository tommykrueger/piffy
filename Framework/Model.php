<?php

namespace Piffy\Framework;

class Model
{
    /**
     * @var array
     */
    public array $_data;

    /**
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        $this->_data = $properties;
        foreach ($properties as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function __get(string $property): mixed
    {
        return $this->_data[$property] ?? null;
    }

    public function __set(string $property, mixed $value): void
    {
        $this->_data[$property] = $value;
    }

    public function __isset(string $property): bool
    {
        $prop = $this->{$property};
        return isset($prop);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        // Note: value of $name is case sensitive.
        echo "Calling object method '$name' "
            . implode(', ', $arguments) . "\n";

        return 'thanks';
    }

    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function getTitle(): string
    {
        return $this->title ?? 0;
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }
}