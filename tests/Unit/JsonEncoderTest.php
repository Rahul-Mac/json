<?php

namespace Rahulmac\Json\Tests\Unit;

use Rahulmac\Json\JsonEncoder;
use Rahulmac\Json\Tests\TestCase;

final class JsonEncoderTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testStringifyEncodesArrayToJson(): void
    {
        $encoder = new JsonEncoder(['foo' => 'bar'], 512, 0);

        $this->assertSame('{"foo":"bar"}', $encoder->stringify());
    }

    /**
     * @throws \JsonException
     */
    public function testPrettifyReturnsPrettyPrintedJson(): void
    {
        $encoder = new JsonEncoder(['foo' => 'bar'], 512, 0);

        $this->assertSame("{\n    \"foo\": \"bar\"\n}", $encoder->prettify());
    }

    /**
     * @throws \JsonException
     */
    public function testWithDepthReturnsNewInstance(): void
    {
        $encoder = new JsonEncoder(['foo' => 'bar'], 512, 0);
        $new     = $encoder->withDepth(10);

        $this->assertNotSame($encoder, $new);
        $this->assertSame('{"foo":"bar"}', $new->stringify());
    }

    /**
     * @throws \JsonException
     */
    public function testWithFlagsOverridesExistingFlags(): void
    {
        $encoder = new JsonEncoder(['foo' => 'bar'], 512, JSON_PRETTY_PRINT);
        $new     = $encoder->withFlags(0);

        $this->assertSame('{"foo":"bar"}', $new->stringify());
    }

    /**
     * @throws \JsonException
     */
    public function testAddFlagsMergesFlags(): void
    {
        $encoder = new JsonEncoder(['foo' => 'bar'], 512, 0);
        $new     = $encoder->addFlags(JSON_PRETTY_PRINT);

        $this->assertStringContainsString("{\n    \"foo\": \"bar\"\n}", $new->stringify());
    }

    public function testStringifyThrowsExceptionForInvalidValue(): void
    {
        $this->expectException(\JsonException::class);

        $resource = \fopen('php://memory', 'r');
        $encoder  = new JsonEncoder($resource, 512, 0);

        $encoder->stringify();
    }
}
