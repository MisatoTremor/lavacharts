<?php

namespace Khill\Lavacharts\Tests\DataTables\Formats;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\DateFormat;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(DateFormat::class)]
#[CoversMethod(DateFormat::class, 'toJson')]
#[CoversMethod(DateFormat::class, 'jsonSerialize')]
class DateFormatTest extends ProvidersTestCase
{
    public $json = '{"formatType":"short","pattern":"Y-m-d","timeZone":"PDT"}';
    private DateFormat $dateFormat;

    public function setUp(): void
    {
        $this->dateFormat = new DateFormat([
            'formatType' => 'short',
            'pattern'    => 'Y-m-d',
            'timeZone'   => 'PDT'
        ]);
    }

    public function testConstructorOptionAssignment(): void
    {
        $this->assertEquals('short', $this->dateFormat['formatType']);
        $this->assertEquals('Y-m-d', $this->dateFormat['pattern']);
        $this->assertEquals('PDT', $this->dateFormat['timeZone']);
    }

    public function testGetType(): void
    {
        $this->dateFormat = new DateFormat;

        $this->assertEquals('DateFormat', $this->dateFormat->getType());
    }

    public function testGetJsClass(): void
    {
        $jsClass = 'google.visualization.DateFormat';

        $this->assertEquals($jsClass, $this->dateFormat->getJsClass());
    }

    public function testToJson(): void
    {
        $this->assertEquals($this->json, $this->dateFormat->toJson());
    }

    public function testJsonSerialization(): void
    {
        $this->assertEquals($this->json, json_encode($this->dateFormat));
    }
}
