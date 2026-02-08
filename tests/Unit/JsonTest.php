<?php

namespace Rahulmac\Json\Tests\Unit;

use Rahul\Json\Json;
use Rahul\Json\JsonDecoder;
use Rahul\Json\JsonEncoder;
use Rahulmac\Json\Tests\TestCase;

final class JsonTest extends TestCase
{
    public function testOfReturnsJsonDecoder(): void
    {
        $this->assertInstanceOf(JsonDecoder::class, Json::of('{"foo": "bar"}'));
    }

    public function testFromReturnsJsonEncoder(): void
    {
        $this->assertInstanceOf(JsonEncoder::class, Json::from(['foo' => 'bar']));
    }
}
