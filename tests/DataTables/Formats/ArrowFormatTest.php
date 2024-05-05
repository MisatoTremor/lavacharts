<?php

namespace Khill\Lavacharts\Tests\DataTables\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\ArrowFormat;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(ArrowFormat::class)]
#[CoversMethod(ArrowFormat::class, 'toJson')]
#[CoversMethod(ArrowFormat::class, 'jsonSerialize')]
class ArrowFormatTest extends ProvidersTestCase
{
    public string $json = '{"base":1}';
    private ArrowFormat $arrowFormat;

    public function setUp(): void
    {
        parent::setUp();

        $this->arrowFormat = new ArrowFormat([
            'base' => 1
        ]);
    }

    public function testConstructorOptionAssignment(): void
    {
        $this->assertEquals(1, $this->arrowFormat['base']);
    }

    public function testGetType(): void
    {
        $this->assertEquals('ArrowFormat', $this->arrowFormat->getType());
    }

    public function testGetJsClass(): void
    {
        $jsClass = 'google.visualization.ArrowFormat';

        $this->assertEquals($jsClass, $this->arrowFormat->getJsClass());
    }

    public function testToJson(): void
    {
        $this->assertEquals($this->json, $this->arrowFormat->toJson());
    }

    public function testJsonSerialization(): void
    {
        $this->assertEquals($this->json, json_encode($this->arrowFormat));
    }
}
