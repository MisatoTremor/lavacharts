<?php

namespace Khill\Lavacharts\Tests\DataTables\Formats;

use Khill\Lavacharts\DataTables\Formats\BarFormat;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(BarFormat::class)]
#[CoversMethod(BarFormat::class, 'toJson')]
#[CoversMethod(BarFormat::class, 'jsonSerialize')]
class BarFormatTest extends ProvidersTestCase
{
    public string $json = '{"base":10,"colorNegative":"red","colorPositive":"green","drawZeroLine":false,"max":100,"min":10,"showValue":true,"width":20}';
    private BarFormat $barFormat;

    public function setUp(): void
    {
        parent::setUp();

        $this->barFormat = new BarFormat([
            'base' => 10,
            'colorNegative' => 'red',
            'colorPositive' => 'green',
            'drawZeroLine' => false,
            'max' => 100,
            'min' => 10,
            'showValue' => true,
            'width' => 20,
        ]);
    }

    public function testConstructorOptionAssignment(): void
    {
        $this->assertEquals(10, $this->barFormat['base']);
        $this->assertEquals('red', $this->barFormat['colorNegative']);
        $this->assertEquals('green', $this->barFormat['colorPositive']);
        $this->assertFalse($this->barFormat['drawZeroLine']);
        $this->assertEquals(10, $this->barFormat['min']);
        $this->assertEquals(100, $this->barFormat['max']);
        $this->assertTrue($this->barFormat['showValue']);
        $this->assertEquals(20, $this->barFormat['width']);
    }

    public function testGetType(): void
    {
        $this->assertEquals('BarFormat', $this->barFormat->getType());
    }

    public function testGetJsClass(): void
    {
        $jsClass = 'google.visualization.BarFormat';

        $this->assertEquals($jsClass, $this->barFormat->getJsClass());
    }

    public function testToJson(): void
    {
        $this->assertEquals($this->json, $this->barFormat->toJson());
    }

    public function testJsonSerialization(): void
    {
        $this->assertEquals($this->json, json_encode($this->barFormat));
    }
}
