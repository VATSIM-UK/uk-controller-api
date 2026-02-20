<?php

namespace App\Services\JsonSchema;

class StandReservationPlanSchemaValidator
{
    private const PAYLOAD_SCHEMA = 'docs/schemas/stand-reservation-plan.schema.json';
    private const API_REQUEST_SCHEMA = 'docs/schemas/stand-reservation-plan-request.schema.json';

    private array $schemaCache = [];

    public function validatePayload(array $payload): array
    {
        return $this->validateAgainstSchema($payload, self::PAYLOAD_SCHEMA);
    }

    public function validateApiRequest(array $request): array
    {
        return $this->validateAgainstSchema($request, self::API_REQUEST_SCHEMA);
    }

    private function validateAgainstSchema(mixed $data, string $schemaPath): array
    {
        $schema = $this->loadSchema($schemaPath);
        return $this->validateNode($schema, $data, '$', $schemaPath, $schema);
    }

    private function validateNode(array $schema, mixed $data, string $path, string $schemaPath, array $rootSchema): array
    {
        if (isset($schema['$ref']) && is_string($schema['$ref'])) {
            [$resolvedSchema, $resolvedPath, $resolvedRoot] = $this->resolveRef($schema['$ref'], $schemaPath, $rootSchema);
            return $this->validateNode($resolvedSchema, $data, $path, $resolvedPath, $resolvedRoot);
        }

        $errors = [];

        if (isset($schema['type'])) {
            $typeErrors = $this->validateType($schema['type'], $data, $path);
            if ($typeErrors !== []) {
                return $typeErrors;
            }
        }

        if (($schema['type'] ?? null) === 'object' && is_array($data) && $this->isAssoc($data)) {
            $errors = array_merge($errors, $this->validateObject($schema, $data, $path, $schemaPath, $rootSchema));
        }

        if (($schema['type'] ?? null) === 'array' && is_array($data) && !$this->isAssoc($data)) {
            $errors = array_merge($errors, $this->validateArray($schema, $data, $path, $schemaPath, $rootSchema));
        }

        if (is_string($data)) {
            if (isset($schema['pattern']) && is_string($schema['pattern'])) {
                if (@preg_match('/' . $schema['pattern'] . '/', $data) !== 1) {
                    $errors[] = sprintf('%s does not match pattern %s', $path, $schema['pattern']);
                }
            }

            if (($schema['format'] ?? null) === 'email' && filter_var($data, FILTER_VALIDATE_EMAIL) === false) {
                $errors[] = sprintf('%s must be a valid email', $path);
            }
        }

        if (($schema['type'] ?? null) === 'integer' && isset($schema['minimum']) && is_int($data) && $data < $schema['minimum']) {
            $errors[] = sprintf('%s must be >= %d', $path, $schema['minimum']);
        }

        if (isset($schema['allOf']) && is_array($schema['allOf'])) {
            foreach ($schema['allOf'] as $subSchema) {
                if (!is_array($subSchema)) {
                    continue;
                }
                $errors = array_merge($errors, $this->validateNode($subSchema, $data, $path, $schemaPath, $rootSchema));
            }
        }

        if (isset($schema['if']) && is_array($schema['if']) && isset($schema['then']) && is_array($schema['then'])) {
            if ($this->validateNode($schema['if'], $data, $path, $schemaPath, $rootSchema) === []) {
                $errors = array_merge($errors, $this->validateNode($schema['then'], $data, $path, $schemaPath, $rootSchema));
            }
        }

        if (isset($schema['oneOf']) && is_array($schema['oneOf'])) {
            $validSchemas = 0;
            foreach ($schema['oneOf'] as $subSchema) {
                if (!is_array($subSchema)) {
                    continue;
                }
                if ($this->validateNode($subSchema, $data, $path, $schemaPath, $rootSchema) === []) {
                    $validSchemas++;
                }
            }

            if ($validSchemas !== 1) {
                $errors[] = sprintf('%s must match exactly one oneOf schema', $path);
            }
        }

        return $errors;
    }

    private function validateObject(array $schema, array $data, string $path, string $schemaPath, array $rootSchema): array
    {
        $errors = [];
        $properties = $schema['properties'] ?? [];

        if (isset($schema['required']) && is_array($schema['required'])) {
            foreach ($schema['required'] as $requiredProperty) {
                if (!array_key_exists($requiredProperty, $data)) {
                    $errors[] = sprintf('%s.%s is required', $path, $requiredProperty);
                }
            }
        }

        if (($schema['additionalProperties'] ?? true) === false && is_array($properties)) {
            foreach ($data as $property => $_value) {
                if (!array_key_exists($property, $properties)) {
                    $errors[] = sprintf('%s.%s is not allowed', $path, $property);
                }
            }
        }

        if (is_array($properties)) {
            foreach ($properties as $property => $propertySchema) {
                if (!array_key_exists($property, $data) || !is_array($propertySchema)) {
                    continue;
                }

                $errors = array_merge(
                    $errors,
                    $this->validateNode($propertySchema, $data[$property], $path . '.' . $property, $schemaPath, $rootSchema)
                );
            }
        }

        return $errors;
    }

    private function validateArray(array $schema, array $data, string $path, string $schemaPath, array $rootSchema): array
    {
        $errors = [];

        if (isset($schema['minItems']) && count($data) < $schema['minItems']) {
            $errors[] = sprintf('%s must have at least %d items', $path, $schema['minItems']);
        }

        if (isset($schema['items']) && is_array($schema['items'])) {
            foreach ($data as $index => $value) {
                $errors = array_merge(
                    $errors,
                    $this->validateNode($schema['items'], $value, sprintf('%s[%d]', $path, $index), $schemaPath, $rootSchema)
                );
            }
        }

        return $errors;
    }

    private function validateType(mixed $type, mixed $data, string $path): array
    {
        $types = is_array($type) ? $type : [$type];

        foreach ($types as $candidateType) {
            if ($this->matchesType($candidateType, $data)) {
                return [];
            }
        }

        return [sprintf('%s must be of type %s', $path, implode('|', $types))];
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

    private function resolveRef(string $ref, string $schemaPath, array $rootSchema): array
    {
        if (str_starts_with($ref, '#/')) {
            return [$this->pointerGet($rootSchema, substr($ref, 2)), $schemaPath, $rootSchema];
        }

        [$file, $pointer] = array_pad(explode('#', $ref, 2), 2, '');
        $resolvedPath = $this->normaliseSchemaPath(dirname($schemaPath) . '/' . $file);
        $resolvedRoot = $this->loadSchema($resolvedPath);

        if ($pointer === '') {
            return [$resolvedRoot, $resolvedPath, $resolvedRoot];
        }

        if (str_starts_with($pointer, '/')) {
            $pointer = substr($pointer, 1);
        }

        return [$this->pointerGet($resolvedRoot, $pointer), $resolvedPath, $resolvedRoot];
    }

    private function pointerGet(array $schema, string $pointer): array
    {
        $segments = $pointer === '' ? [] : explode('/', $pointer);
        $value = $schema;

        foreach ($segments as $segment) {
            $segment = str_replace(['~1', '~0'], ['/', '~'], $segment);

            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
                continue;
            }

            return [];
        }

        return is_array($value) ? $value : [];
    }

    private function loadSchema(string $schemaPath): array
    {
        $schemaPath = $this->normaliseSchemaPath($schemaPath);

        if (!isset($this->schemaCache[$schemaPath])) {
            $decoded = json_decode((string) file_get_contents(base_path($schemaPath)), true);
            $this->schemaCache[$schemaPath] = is_array($decoded) ? $decoded : [];
        }

        return $this->schemaCache[$schemaPath];
    }

    private function normaliseSchemaPath(string $schemaPath): string
    {
        $schemaPath = str_replace('\\', '/', $schemaPath);
        $parts = [];

        foreach (explode('/', $schemaPath) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($parts);
                continue;
            }

            $parts[] = $part;
        }

        return implode('/', $parts);
    }

    private function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
