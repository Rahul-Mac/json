<?php

namespace Rahulmac\Json;

use JsonException;

final class JsonDecoder
{
    private array $value;

    public function __construct(
        private string $json,
        private int    $depth,
        private int    $flags,
    ) {
    }

    /**
     * @throws JsonException
     */
    private function fetch(string $key): mixed
    {
        if (! isset($this->value)) {
            $this->value = $this->toArray();
        }

        $value = $this->value;

        $segments = \explode('.', $key);

        foreach ($segments as $segment) {
            if (\is_array($value) && \array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                throw new \InvalidArgumentException("Invalid key [$key].");
            }
        }

        return $value;
    }

    /**
     * @throws JsonException
     */
    public function parse(): mixed
    {
        return \json_decode(json: $this->json, depth: $this->depth, flags: $this->flags | JSON_THROW_ON_ERROR);
    }

    public function withDepth(int $depth): self
    {
        return new self($this->json, $depth, $this->flags);
    }

    public function withFlags(int $flags): self
    {
        return new self($this->json, $this->depth, $flags);
    }

    public function addFlags(int $flags): self
    {
        return $this->withFlags($this->flags | $flags);
    }

    /**
     * @throws JsonException
     */
    public function toArray(): array
    {
        return $this->addFlags(JSON_OBJECT_AS_ARRAY)->parse();
    }

    public function isValid(): bool
    {
        if (PHP_VERSION_ID >= 80300) {
            return \json_validate($this->json, $this->depth, $this->flags);
        }

        try {
            $this->parse();

            return true;
        } catch (JsonException) {
            return false;
        }
    }

    /**
     * @throws JsonException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return $this->fetch($key);
        } catch (\InvalidArgumentException) {
            return $default;
        }
    }

    /**
     * @throws JsonException
     */
    public function has(string $key): bool
    {
        try {
            $this->fetch($key);

            return true;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    /**
     * @throws JsonException
     */
    public function asInt(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    /**
     * @throws JsonException
     */
    public function asFloat(string $key, float $default = 0.0): float
    {
        return (float) $this->get($key, $default);
    }

    /**
     * @throws JsonException
     */
    public function asString(string $key, string $default = ''): string
    {
        return (string) $this->get($key, $default);
    }

    /**
     * @throws JsonException
     */
    public function asBool(string $key, bool $default = false): bool
    {
        return (bool) $this->get($key, $default);
    }

    /**
     * @throws JsonException
     */
    public function asArray(string $key, array $default = []): array
    {
        return (array) $this->get($key, $default);
    }
}
