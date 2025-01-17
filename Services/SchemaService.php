<?php

namespace Piffy\Services;

class SchemaService
{
    public array $schemas = [];

    public static ?SchemaService $_instance = null;

    public static function getInstance(): ?SchemaService
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function registerSchema($schemaName, $schema): void
    {
        $schema = file_get_contents($schema, true);
        $schema = json_decode($schema, true);
        $this->schemas[$schemaName] = (object)$schema;
    }

    public function getSchemas(string $name): object
    {
        return $this->schemas[$name];
    }
}

