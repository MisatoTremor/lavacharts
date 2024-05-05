<?php

namespace Khill\Lavacharts\Tests\DataTables\Rows;

use Khill\Lavacharts\DataTables\Rows\Row;
use Khill\Lavacharts\Exceptions\InvalidColumnIndex;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use Carbon\Carbon;
use Khill\Lavacharts\DataTables\Cells\DateCell;

#[CoversMethod(Row::class, '__construct')]
#[CoversMethod(Row::class, 'getCell')]
#[CoversMethod(Row::class, 'jsonSerialize')]
class RowTest extends ProvidersTestCase
{
    public function testConstructorWithNonCarbonValues(): void
    {
        $row = new Row(['bob', 1, 2.0]);

        $values = $this->inspect($row, 'values');

        $this->assertEquals('bob', $values[0]->getValue());
        $this->assertEquals(1, $values[1]->getValue());
        $this->assertEquals(2.0, $values[2]->getValue());
    }

    public function testConstructorWithCarbon(): void
    {
        $mockCarbon = Mockery::mock(Carbon::class)->makePartial();

        $row = new Row([$mockCarbon, 1, 2.0]);

        $values = $this->inspect($row, 'values');

        $this->assertInstanceOf(DateCell::class, $values[0]);
        $this->assertEquals(1, $values[1]->getValue());
        $this->assertEquals(2.0, $values[2]->getValue());
    }

    #[Depends('testConstructorWithCarbon')]
    public function testGetColumnValue(): void
    {
        $mockCarbon = Mockery::mock(Carbon::class)->makePartial();

        $row = new Row([$mockCarbon, 1, 2.0]);

        $this->assertInstanceOf(DateCell::class, $row->getCell(0));
        $this->assertEquals(1, $row->getCell(1)->getValue());
        $this->assertEquals(2.0, $row->getCell(2)->getValue());
    }

    #[Depends('testConstructorWithCarbon')]
    #[DataProvider('nonIntProvider')]
    public function testGetColumnValueWithBadType($badTypes): void
    {
        $this->expectException(InvalidColumnIndex::class);
        $mockCarbon = Mockery::mock(Carbon::class)->makePartial();

        $row = new Row([$mockCarbon, 1, 2.0]);

        $row->getCell($badTypes);
    }

    #[Depends('testConstructorWithCarbon')]
    public function testGetColumnValueWithInvalidColumnIndex(): void
    {
        $this->expectException(InvalidColumnIndex::class);
        $row = new Row([1]);

        $row->getCell(41);
    }

    #[Depends('testConstructorWithCarbon')]
    public function testJsonSerialization(): void
    {
        $mockCarbon = Mockery::mock('\Carbon\Carbon[parse]', ['1988-03-24 1:23:45']);

        $row = new Row([$mockCarbon, 1, 2.1]);

        $json = '{"c":[{"v":"Date(1988,2,24,1,23,45)"},{"v":1},{"v":2.1}]}';

        $this->assertEquals($json, json_encode($row));
    }
}
