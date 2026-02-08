<?php

namespace Rahul\Json;

final class JsonEncoder
{
    public function __construct(
        private mixed $value,
        private int   $depth,
        private int   $flags,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function stringify(): string
    {
        return \json_encode($this->value, $this->flags | JSON_THROW_ON_ERROR, $this->depth);
    }

    public function withDepth(int $depth): self
    {
        return new self($this->value, $depth, $this->flags);
    }

    public function withFlags(int $flags): self
    {
        return new self($this->value, $this->depth, $flags);
    }

    public function addFlags(int $flags): self
    {
        return $this->withFlags($this->flags | $flags);
    }

    /**
     * @throws \JsonException
     */
    public function prettify(): string
    {
        return $this->addFlags(JSON_PRETTY_PRINT)->stringify();
    }
}
