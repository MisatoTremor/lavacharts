<?php

namespace Khill\Lavacharts\Tests\DataTables\Columns;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Columns\ColumnFactory;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

#[CoversMethod(ColumnFactory::class, 'create')]
class ColumnFactoryTest extends ProvidersTestCase
{
    /**
     * @var \Khill\Lavacharts\DataTables\Columns\ColumnFactory
     */
    public $columnFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->columnFactory = new ColumnFactory;
    }

    #[DataProvider('columnTypeProvider')]
    public function testCreateColumnsWithType($columnType): void
    {
        $column = $this->columnFactory->create($columnType);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\Column', $column);
        $this->assertEquals($columnType, $this->inspect($column, 'type'));
    }

    public function testCreateColumnsWithBadValue(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnType::class);
        $this->columnFactory->create('milkshakes');
    }

    #[DataProvider('nonStringProvider')]
    public function testCreateColumnsWithBadTypes($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnType::class);
        $this->columnFactory->create($badTypes);
    }

    #[Depends('testCreateColumnsWithType')]
    #[DataProvider('columnTypeProvider')]
    public function testCreateColumnsWithTypeAndLabel($columnType): void
    {
        $column = $this->columnFactory->create($columnType, 'Label');

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\Column', $column);
        $this->assertEquals($columnType, $this->inspect($column, 'type'));
        $this->assertEquals('Label', $this->inspect($column, 'label'));
    }

    #[Depends('testCreateColumnsWithTypeAndLabel')]
    #[DataProvider('columnTypeProvider')]
    public function testCreateColumnsWithTypeAndLabelAndFormat($columnType): void
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $column = $this->columnFactory->create($columnType, 'Label', $mockFormat);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\Column', $column);
        $this->assertEquals($columnType, $this->inspect($column, 'type'));
        $this->assertEquals('Label', $this->inspect($column, 'label'));
        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\NumberFormat', $this->inspect($column, 'format'));
    }

    #[Depends('testCreateColumnsWithTypeAndLabelAndFormat')]
    #[DataProvider('columnTypeProvider')]
    public function testCreateColumnsWithTypeAndLabelAndFormatAndRole($columnType): void
    {
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $column = $this->columnFactory->create($columnType, 'Label', $mockFormat, 'interval');

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Columns\Column', $column);
        $this->assertEquals($columnType, $this->inspect($column, 'type'));
        $this->assertEquals('Label', $this->inspect($column, 'label'));
        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\NumberFormat', $this->inspect($column, 'format'));
        $this->assertInstanceOf('\Khill\Lavacharts\Values\Role', $this->inspect($column, 'role'));
        //@TODO remove me
        //$this->assertEquals('interval', $this->inspect($column, 'role'));
    }

    #[Depends('testCreateColumnsWithTypeAndLabelAndFormatAndRole')]
    public function testCreateColumnsWithTypeAndLabelAndFormatAndRoleWithBadRole(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnRole::class);
        $mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();

        $this->columnFactory->create('number', 'Label', $mockFormat, 'tacos');
    }
}
