<?php

namespace Khill\Lavacharts\Tests\DataTables\Cells;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Cells\Cell;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

#[CoversMethod(Cell::class, '__construct')]
#[CoversMethod(Cell::class, 'getValue')]
#[CoversMethod(Cell::class, 'getFormat')]
#[CoversMethod(Cell::class, 'getOptions')]
#[CoversMethod(Cell::class, 'jsonSerialize')]
class CellTest extends ProvidersTestCase
{
    public Cell $Cell;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testConstructorArgs(): void
    {
        $column = new Cell(1, 'low', ['textstyle' => ['fontName' => 'Arial']]);

        $this->assertEquals(1, $this->inspect($column, 'v'));
        $this->assertEquals('low', $this->inspect($column, 'f'));
        // Virtual property
        $this->assertIsArray($column->p);
    }

    #[DataProvider('nonStringProvider')]
    public function testConstructorArgFormatWithBadType($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidParamType::class);
        new Cell(1, $badTypes);
    }

    #[Depends('testConstructorArgs')]
    public function testGetValue(): void
    {
        $column = new Cell(1);

        $this->assertEquals(1, $column->getValue());
    }

    #[Depends('testConstructorArgs')]
    public function testGetFormat(): void
    {
        $column = new Cell(1, 'low');

        $this->assertEquals('low', $column->getFormat());
    }

    #[Depends('testConstructorArgs')]
    public function testGetOptions(): void
    {
        $column = new Cell(1, 'low', ['textstyle' => ['fontName' => 'Arial']]);

        $this->assertIsArray($column->getOptions());
    }

    #[Depends('testConstructorArgs')]
    public function testJsonSerialization(): void
    {
        $column = new Cell(1, 'low', ['textstyle' => ['fontName' => 'Arial']]);

        $json = '{"v":1,"f":"low","p":{"textstyle":{"fontName":"Arial"}}}';

        $this->assertEquals($json, json_encode($column));
    }
}
