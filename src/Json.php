<?php

namespace Rahul\Json;

final class Json
{
    public static function of(string $json, int $depth = 512, int $flags = 0): JsonDecoder
    {
        return new JsonDecoder($json, $depth, $flags);
    }

    public static function from(mixed $value, int $depth = 512, int $flags = 0): JsonEncoder
    {
        return new JsonEncoder($value, $depth, $flags);
    }
}