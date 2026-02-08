<?php

namespace Rahulmac\Json\Tests\Unit;

use Rahulmac\Json\JsonDecoder;
use Rahulmac\Json\Tests\TestCase;

final class JsonDecoderTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testParseReturnsStdClassByDefault(): void
    {
        $decoder = new JsonDecoder('{"foo":"bar"}', 512, 0);

        $result = $decoder->parse();

        $this->assertIsObject($result);
        $this->assertSame('bar', $result->foo);
    }

    /**
     * @throws \JsonException
     */
    public function testToArrayReturnsAssociativeArray(): void
    {
        $decoder = new JsonDecoder('{"foo":"bar"}', 512, 0);

        $result = $decoder->toArray();

        $this->assertSame(['foo' => 'bar'], $result);
    }

    /**
     * @throws \JsonException
     */
    public function testToObjectReturnsObject(): void
    {
        $decoder = new JsonDecoder('{"foo":"bar"}', 512, 0);

        $result = $decoder->toObject();

        $this->assertIsObject($result);
        $this->assertSame('bar', $result->foo);
    }

    /**
     * @throws \JsonException
     */
    public function testWithDepthReturnsNewInstance(): void
    {
        $decoder = new JsonDecoder('{"foo":"bar"}', 512, 0);
        $new = $decoder->withDepth(10);

        $this->assertNotSame($decoder, $new);
        $this->assertSame(['foo' => 'bar'], $new->toArray());
    }

    /**
     * @throws \JsonException
     */
    public function testWithFlagsOverridesFlags(): void
    {
        $decoder = new JsonDecoder('{"foo":"bar"}', 512, JSON_OBJECT_AS_ARRAY);
        $new = $decoder->withFlags(0);

        $this->assertIsObject($new->parse());
    }

    /**
     * @throws \JsonException
     */
    public function testAddFlagsMergesFlags(): void
    {
        $decoder = new JsonDecoder('{"foo":"bar"}', 512, 0);
        $new = $decoder->addFlags(JSON_OBJECT_AS_ARRAY);

        $this->assertSame(['foo' => 'bar'], $new->parse());
    }

    public function testParseThrowsExceptionForInvalidJson(): void
    {
        $this->expectException(\JsonException::class);

        $decoder = new JsonDecoder('{invalid json}', 512, 0);
        $decoder->parse();
    }

    public function testToArrayThrowsExceptionForInvalidJson(): void
    {
        $this->expectException(\JsonException::class);

        $decoder = new JsonDecoder('{invalid json}', 512, 0);
        $decoder->toArray();
    }

    public function testToObjectThrowsExceptionForInvalidJson(): void
    {
        $this->expectException(\JsonException::class);

        $decoder = new JsonDecoder('{invalid json}', 512, 0);
        $decoder->toObject();
    }

    public function testIsValidReturnsTrueForValidJson(): void
    {
        $decoder = new JsonDecoder('{"foo":"bar"}', 512, 0);

        $this->assertTrue($decoder->isValid());
    }

    public function testIsValidReturnsFalseForInvalidJson(): void
    {
        $decoder = new JsonDecoder('{invalid json}', 512, 0);

        $this->assertFalse($decoder->isValid());
    }

    public function testDepthLimitIsRespected(): void
    {
        $this->expectException(\JsonException::class);

        $json = '{"a":{"b":{"c":{"d":"e"}}}}';

        $decoder = new JsonDecoder($json, 2, 0);
        $decoder->parse();
    }
}
