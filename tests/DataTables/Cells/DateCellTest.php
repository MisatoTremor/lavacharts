<?php

namespace Khill\Lavacharts\Tests\DataTables\Cells;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Cells\DateCell;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

#[CoversMethod(DateCell::class, '__construct')]
#[CoversMethod(DateCell::class, '__toString')]
#[CoversMethod(DateCell::class, 'jsonSerialize')]
#[CoversMethod(DateCell::class, 'parseString')]
class DateCellTest extends ProvidersTestCase
{
    public function testConstructorArgs(): void
    {
        $mockCarbon = \Mockery::mock('\Carbon\Carbon[parse]', ['2015-09-04 9:31:00']);

        $column = new DateCell($mockCarbon, 'start', ['color'=>'red']);

        $this->assertInstanceOf('Carbon\Carbon', $this->inspect($column, 'v'));
        $this->assertEquals('start', $this->inspect($column, 'f'));

        //@TODO fix this
        //$this->assertTrue(is_array($this->inspect($column, 'p')));
    }

    public function testParseStringWithNoFormat(): void
    {
        $cell = DateCell::parseString('3/24/1988 8:01:05');
        $this->assertEquals('Date(1988,2,24,8,1,5)', (string) $cell);

        $cell = DateCell::parseString('March 24th, 1988 8:01:05');
        $this->assertEquals('Date(1988,2,24,8,1,5)', (string) $cell);
    }

    public function testParseStringWithFormat(): void
    {

        $cell = DateCell::parseString('5:45pm on Saturday 24th March 2012', 'g:ia \o\n l jS F Y');

        $this->assertEquals('Date(2012,2,24,17,45,0)', (string) $cell);
    }

    public function testParseStringWithBadDateTimeString(): void
    {
        $this->expectException(\Exception::class);
        DateCell::parseString('132/06/199210');
    }

    #[DataProvider('nonStringOrNullProvider')]
    public function testParseStringWithBadTypesForDateTime($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidDateTimeString::class);
        DateCell::parseString($badTypes);
    }

    public function testParseStringWithBadFormatString(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidDateTimeFormat::class);
        DateCell::parseString('1/2/2003', 'sushi');
    }

    #[DataProvider('nonStringProvider')]
    public function testParseStringWithBadTypesForFormat(): void
    {
        self::assertInstanceOf(DateCell::class, DateCell::parseString('1/2/2003', ['imnotaformat']));
    }

    #[Depends('testConstructorArgs')]
    public function testJsonSerialization(): void
    {
        $mockCarbon = \Mockery::mock('\Carbon\Carbon[parse]', ['2015-09-04 9:31:00']);

        $cell = new DateCell($mockCarbon);

        $this->assertEquals('{"v":"Date(2015,8,4,9,31,0)"}', json_encode($cell));
    }
}
