<?php

namespace Rahulmac\Json\Tests\Unit;

use Rahulmac\Json\JsonDecoder;
use Rahulmac\Json\Tests\TestCase;

final class JsonDecoderTest extends TestCase
{
    protected function setUp(): void
    {
        $this->json = <<<JSON
        {
            "id": 1,
            "name": "Alice",
            "active": true,
            "balance": 23.45,
            "roles": ["admin", "editor"],
            "meta": {
                "age": 67,
                "location": "NY"
            }
        }
        JSON;

        $this->decoder = new JsonDecoder($this->json, 512, 0);

        parent::setUp();
    }

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

    /**
     * @throws \JsonException
     */
    public function testGetReturnsValueForKey(): void
    {
        $this->assertEquals(1, $this->decoder->get('id'));
        $this->assertEquals('Alice', $this->decoder->get('name'));
        $this->assertTrue($this->decoder->get('active'));
    }

    /**
     * @throws \JsonException
     */
    public function testGetReturnsValueForNestedKey(): void
    {
        $this->assertEquals(67, $this->decoder->get('meta.age'));
        $this->assertEquals('NY', $this->decoder->get('meta.location'));
    }

    /**
     * @throws \JsonException
     */
    public function testGetMissingKeyReturnsDefault(): void
    {
        $this->assertEquals('default', $this->decoder->get('unknown', 'default'));
        $this->assertNull($this->decoder->get('unknown'));
        $this->assertEquals('default', $this->decoder->get('meta.unknown', 'default'));
        $this->assertNull($this->decoder->get('meta.unknown'));
    }

    /**
     * @throws \JsonException
     */
    public function testAsInt(): void
    {
        $this->assertSame(1, $this->decoder->asInt('id'));
        $this->assertSame(0, $this->decoder->asInt('unknown'));
        $this->assertSame(12, $this->decoder->asInt('unknown', 12));
        $this->assertSame(0, $this->decoder->asInt('meta.unknown'));
        $this->assertSame(42, $this->decoder->asInt('meta.unknown', 42));
    }

    /**
     * @throws \JsonException
     */
    public function testAsFloat(): void
    {
        $this->assertSame(23.45, $this->decoder->asFloat('balance'));
        $this->assertSame(0.0, $this->decoder->asFloat('unknown'));
        $this->assertSame(1.7, $this->decoder->asFloat('unknown', 1.7));
        $this->assertSame(0.0, $this->decoder->asFloat('meta.unknown'));
        $this->assertSame(8.9, $this->decoder->asFloat('meta.unknown', 8.9));
    }

    /**
     * @throws \JsonException
     */
    public function testAsString(): void
    {
        $this->assertSame('Alice', $this->decoder->asString('name'));
        $this->assertSame('', $this->decoder->asString('unknown'));
        $this->assertSame('Foo', $this->decoder->asString('unknown', 'Foo'));
        $this->assertSame('', $this->decoder->asString('meta.unknown'));
        $this->assertSame('Bar', $this->decoder->asString('meta.unknown', 'Bar'));
    }

    /**
     * @throws \JsonException
     */
    public function testAsBool(): void
    {
        $this->assertTrue($this->decoder->asBool('active'));
        $this->assertFalse($this->decoder->asBool('unknown'));
        $this->assertTrue($this->decoder->asBool('unknown', true));
        $this->assertFalse($this->decoder->asBool('meta.unknown'));
        $this->assertTrue($this->decoder->asBool('meta.unknown', true));
    }

    /**
     * @throws \JsonException
     */
    public function testAsArray(): void
    {
        $this->assertSame(['admin', 'editor'], $this->decoder->asArray('roles'));
        $this->assertSame([], $this->decoder->asArray('unknown'));
        $this->assertSame(['foo'], $this->decoder->asArray('unknown', ['foo']));
        $this->assertSame([], $this->decoder->asArray('meta.unknown'));
        $this->assertSame(['bar'], $this->decoder->asArray('meta.unknown', ['bar']));
    }

    /**
     * @throws \JsonException
     */
    public function testHas(): void
    {
        $this->assertTrue($this->decoder->has('id'));
        $this->assertTrue($this->decoder->has('meta.age'));
        $this->assertFalse($this->decoder->has('unknown'));
        $this->assertFalse($this->decoder->has('meta.unknown'));
    }
}
