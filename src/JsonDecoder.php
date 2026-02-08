<?php

namespace Rahul\Json;

use JsonException;

final class JsonDecoder
{
    private mixed $value;

    public function __construct(
        private string $json,
        private int    $depth,
        private int    $flags,
    ) {
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

    /**
     * @throws JsonException
     */
    public function toObject(): object
    {
        return (object) $this->parse();
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
}
