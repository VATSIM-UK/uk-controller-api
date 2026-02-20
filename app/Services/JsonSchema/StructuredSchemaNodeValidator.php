<?php

namespace App\Services\JsonSchema;

class StructuredSchemaNodeValidator
{
    public function validateType(mixed $type, mixed $data, string $path): array
    {
        $types = is_array($type) ? $type : [$type];

        foreach ($types as $candidateType) {
            if ($this->matchesType($candidateType, $data)) {
                return [];
            }
        }

        return [sprintf('%s must be of type %s', $path, implode('|', $types))];
    }

    public function validateStructuredData(
        array $schema,
        mixed $data,
        string $path,
        string $schemaPath,
        array $rootSchema,
        callable $validateNode
    ): array {
        if (is_array($data) && $this->isAssoc($data) && $this->shouldValidateObject($schema)) {
            return $this->validateObject($schema, $data, $path, $schemaPath, $rootSchema, $validateNode);
        }

        if (is_array($data) && !$this->isAssoc($data) && $this->shouldValidateArray($schema)) {
            return $this->validateArray($schema, $data, $path, $schemaPath, $rootSchema, $validateNode);
        }

        return [];
    }

    private function shouldValidateObject(array $schema): bool
    {
        return ($schema['type'] ?? null) === 'object'
            || isset($schema['properties'])
            || isset($schema['required'])
            || array_key_exists('additionalProperties', $schema);
    }

    private function shouldValidateArray(array $schema): bool
    {
        return ($schema['type'] ?? null) === 'array'
            || isset($schema['items'])
            || isset($schema['minItems']);
    }

    private function validateObject(
        array $schema,
        array $data,
        string $path,
        string $schemaPath,
        array $rootSchema,
        callable $validateNode
    ): array {
        $properties = is_array($schema['properties'] ?? null) ? $schema['properties'] : [];

        return array_merge(
            $this->validateRequiredProperties($schema['required'] ?? null, $data, $path),
            $this->validateDisallowedProperties($schema['additionalProperties'] ?? true, $properties, $data, $path),
            $this->validateObjectProperties($properties, $data, $path, $schemaPath, $rootSchema, $validateNode),
        );
    }

    private function validateRequiredProperties(mixed $required, array $data, string $path): array
    {
        if (!is_array($required)) {
            return [];
        }

        $errors = [];

        foreach ($required as $requiredProperty) {
            if (!array_key_exists($requiredProperty, $data)) {
                $errors[] = sprintf('%s.%s is required', $path, $requiredProperty);
            }
        }

        return $errors;
    }

    private function validateDisallowedProperties(mixed $additionalProperties, array $properties, array $data, string $path): array
    {
        if ($additionalProperties !== false) {
            return [];
        }

        $errors = [];

        foreach ($data as $property => $_value) {
            if (!array_key_exists($property, $properties)) {
                $errors[] = sprintf('%s.%s is not allowed', $path, $property);
            }
        }

        return $errors;
    }

    private function validateObjectProperties(
        array $properties,
        array $data,
        string $path,
        string $schemaPath,
        array $rootSchema,
        callable $validateNode
    ): array {
        $errors = [];

        foreach ($properties as $property => $propertySchema) {
            if (!array_key_exists($property, $data) || !is_array($propertySchema)) {
                continue;
            }

            $errors = array_merge(
                $errors,
                $validateNode($propertySchema, $data[$property], $path . '.' . $property, $schemaPath, $rootSchema)
            );
        }

        return $errors;
    }

    private function validateArray(
        array $schema,
        array $data,
        string $path,
        string $schemaPath,
        array $rootSchema,
        callable $validateNode
    ): array {
        $errors = [];

        if (isset($schema['minItems']) && count($data) < $schema['minItems']) {
            $errors[] = sprintf('%s must have at least %d items', $path, $schema['minItems']);
        }

        if (isset($schema['items']) && is_array($schema['items'])) {
            foreach ($data as $index => $value) {
                $errors = array_merge(
                    $errors,
                    $validateNode($schema['items'], $value, sprintf('%s[%d]', $path, $index), $schemaPath, $rootSchema)
                );
            }
        }

        return $errors;
    }

    private function matchesType(string $type, mixed $data): bool
    {
        return match ($type) {
            'object' => is_array($data) && $this->isAssoc($data),
            'array' => is_array($data) && !$this->isAssoc($data),
            'string' => is_string($data),
            'integer' => is_int($data),
            'number' => is_numeric($data),
            'boolean' => is_bool($data),
            'null' => $data === null,
            default => false,
        };
    }

    private function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
