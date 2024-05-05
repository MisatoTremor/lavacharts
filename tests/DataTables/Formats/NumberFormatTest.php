<?php

namespace Khill\Lavacharts\Tests\DataTables\Formats;

use Khill\Lavacharts\DataTables\Formats\DateFormat;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Formats\NumberFormat;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(NumberFormat::class)]
#[CoversMethod(DateFormat::class, 'toJson')]
#[CoversMethod(DateFormat::class, 'jsonSerialize')]
#[\AllowDynamicProperties]
class NumberFormatTest extends ProvidersTestCase
{
    public $json = '{"decimalSymbol":".","fractionDigits":2,"groupingSymbol":",","negativeColor":"red","negativeParens":true,"pattern":"#,###","prefix":"$","suffix":"\/hr"}';
    private NumberFormat $numberFormat;

    public function setUp(): void
    {
        parent::setUp();

        $this->numberFormat = new NumberFormat([
            'decimalSymbol'  => '.',
            'fractionDigits' => 2,
            'groupingSymbol' => ',',
            'negativeColor'  => 'red',
            'negativeParens' => true,
            'pattern'        => '#,###',
            'prefix'         => '$',
            'suffix'         => '/hr'
        ]);
    }

    public function testConstructorOptionAssignment(): void
    {
        $this->assertEquals('.', $this->numberFormat['decimalSymbol']);
        $this->assertEquals(2, $this->numberFormat['fractionDigits']);
        $this->assertEquals(',', $this->numberFormat['groupingSymbol']);
        $this->assertEquals('red', $this->numberFormat['negativeColor']);
        $this->assertTrue($this->numberFormat['negativeParens']);
        $this->assertEquals('#,###', $this->numberFormat['pattern']);
        $this->assertEquals('$', $this->numberFormat['prefix']);
        $this->assertEquals('/hr', $this->numberFormat['suffix']);
    }

    public function testGetType(): void
    {
        $numberFormat = new NumberFormat;

        $this->assertEquals('NumberFormat', $numberFormat->getType());
    }

    public function testGetJsClass(): void
    {
        $jsClass = 'google.visualization.NumberFormat';

        $this->assertEquals($jsClass, $this->numberFormat->getJsClass());
    }

    public function testToJson(): void
    {
        $this->assertEquals($this->json, $this->numberFormat->toJson());
    }

    public function testJsonSerialization(): void
    {
        $this->assertEquals($this->json, json_encode($this->numberFormat));
    }
}
