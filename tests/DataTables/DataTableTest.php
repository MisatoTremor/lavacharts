<?php

namespace Khill\Lavacharts\Tests\DataTables;

use Carbon\Carbon;
use Khill\Lavacharts\DataTables\Columns\ColumnFactory;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use const DATATABLE_NS;

#[CoversMethod(DataTable::class, 'addBooleanColumn')]
#[CoversMethod(DataTable::class, 'addStringColumn')]
#[CoversMethod(DataTable::class, 'addNumberColumn')]
#[CoversMethod(DataTable::class, 'addDateColumn')]
#[CoversMethod(DataTable::class, 'addDateTimeColumn')]
#[CoversMethod(DataTable::class, 'addTimeOfDayColumn')]
#[CoversMethod(DataTable::class, 'addColumn')]
#[CoversMethod(DataTable::class, 'addColumns')]
#[CoversMethod(DataTable::class, 'addRoleColumn')]
#[CoversMethod(DataTable::class, 'dropColumn')]
#[CoversMethod(DataTable::class, 'formatColumn')]
#[CoversMethod(DataTable::class, 'formatColumns')]
#[CoversMethod(DataTable::class, 'getFormattedColumns')]
#[CoversMethod(DataTable::class, 'hasFormattedColumns')]
#[CoversMethod(DataTable::class, 'getColumn')]
#[CoversMethod(DataTable::class, 'getColumns')]
#[CoversMethod(DataTable::class, 'getColumnLabel')]
#[CoversMethod(DataTable::class, 'getColumnLabels')]
#[CoversMethod(DataTable::class, 'getColumnType')]
#[CoversMethod(DataTable::class, 'getColumnsByType')]
#[CoversMethod(DataTable::class, 'addRow')]
#[CoversMethod(DataTable::class, 'addRows')]
#[CoversMethod(DataTable::class, 'getRowCount')]
class DataTableTest extends ProvidersTestCase
{
    /**
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    public $DataTable;

    public static array $columnTypes = [
        'BooleanColumn',
        'NumberColumn',
        'StringColumn',
        'DateColumn',
        'DateTimeColumn',
        'TimeOfDayColumn',
    ];

    public static array $columnLabels = [
        'tooltip',
        'Admin',
        'Unique Visitors',
        'People In Group',
        'Most Commits',
        'Entries Edited',
        'Peak Usage Hours',
    ];

    public $tzLA = 'America/Los_Angeles';

    public $tzNY = 'America/New_York';

    public function setUp(): void
    {
        parent::setUp();

        date_default_timezone_set($this->tzLA);

        $this->DataTable = new DataTable();
    }

    public function privateColumnAccess($index = null)
    {
        $cols = $this->inspect($this->DataTable, 'cols');

        return is_int($index) ? $cols[$index] : $cols;
    }

    public function privateRowAccess($index = null)
    {
        $rows = $this->inspect($this->DataTable, 'rows');

        return is_int($index) ? $rows[$index] : $rows;
    }

    public function privateCellAccess($rowIndex, $cellIndex)
    {
        $row = $this->privateRowAccess($rowIndex);

        return $row[$cellIndex];
    }

    public static function columnCreationNameProvider(): array
    {
        return array_map(function ($columnName) {
            return [$columnName];
        }, self::$columnTypes);
    }

    public static function columnTypeAndLabelProvider(): array
    {
        $columns = [];

        foreach (ColumnFactory::$types as $index => $type) {
            $columns[] = [$type, self::$columnLabels[$index]];
        }

        return $columns;
    }

    public function testDefaultTimezoneUponCreation(): void
    {
        $tz = $this->inspect($this->DataTable, 'timezone');

        $this->assertEquals($this->tzLA, $tz->getName());
    }

    public function testSetTimezoneWithConstructor(): void
    {
        $datatable = new DataTable($this->tzNY);

        $tz = $this->inspect($datatable, 'timezone');

        $this->assertEquals($this->tzNY, $tz->getName());
    }

    public function testSetTimezoneMethod(): void
    {
        $this->DataTable->setTimezone($this->tzNY);

        $tz = $this->inspect($this->DataTable, 'timezone');

        $this->assertEquals($this->tzNY, $tz->getName());
    }

    #[Depends('testSetTimezoneMethod')]
    #[DataProvider('nonStringProvider')]
    public function testSetTimezoneWithBadType($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidTimeZone::class);
        $this->DataTable->setTimezone($badTypes);
    }

    #[Depends('testSetTimezoneMethod')]
    public function testSetTimezoneWithInvalidTimezone($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidTimeZone::class);
        $this->DataTable->setTimezone('Murica');
    }

    #[Depends('testSetTimezoneMethod')]
    public function testGetTimezoneMethod(): void
    {
        $this->DataTable->setTimezone($this->tzNY);

        $this->assertInstanceOf('DateTimeZone', $this->DataTable->getTimezone());
        $this->assertEquals($this->tzNY, $this->DataTable->getTimezone()->getName());
    }

    public function testSetDateTimeFormat(): void
    {
        $this->DataTable->setDateTimeFormat('YYYY-mm-dd');

        $format = $this->inspect($this->DataTable, 'dateTimeFormat');

        $this->assertEquals('YYYY-mm-dd', $format);
    }

    #[Depends('testSetDateTimeFormat')]
    #[DataProvider('nonStringProvider')]
    public function testSetDateTimeFormatWithBadTypes($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidDateTimeFormat::class);
        $this->DataTable->setDateTimeFormat($badTypes);
    }

    #[Depends('testSetDateTimeFormat')]
    public function testGetDateTimeFormat(): void
    {
        $this->DataTable->setDateTimeFormat('YYYY-mm-dd');

        $this->assertEquals('YYYY-mm-dd', $this->DataTable->getDateTimeFormat());
    }

    #[DataProvider('columnTypeProvider')]
    public function testAddColumnByType($columnType): void
    {
        $this->DataTable->addColumn($columnType);

        $column = $this->privateColumnAccess(0);

        $this->assertEquals($columnType, $column->getType());
    }

    #[Depends('testAddColumnByType')]
    #[DataProvider('columnTypeProvider')]
    public function testAddColumnByTypeInArray($columnType): void
    {
        $this->DataTable->addColumn([$columnType]);

        $column = $this->privateColumnAccess(0);

        $this->assertEquals($columnType, $column->getType());
    }

    public function testAddColumnWithBadTypes(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidConfigValue::class);
        $this->DataTable->addColumn(1);
        $this->DataTable->addColumn(1.1);
        $this->DataTable->addColumn(false);
        $this->DataTable->addColumn(new \stdClass());
    }

    #[DataProvider('columnCreationNameProvider')]
    public function testAddColumnViaNamedAlias($columnType): void
    {
        call_user_func([$this->DataTable, 'add'.$columnType]);

        $column = $this->privateColumnAccess(0);

        $type = strtolower(str_replace('Column', '', $columnType));

        $this->assertEquals($type, $column->getType());
    }

    public function testAddColumnsWithBadTypesInArray(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnDefinition::class);
        $this->DataTable->addColumns([
            5.6,
            15.6244,
            'hotdogs',
        ]);
    }

    public function testAddColumnsWithBadValuesInArray(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnType::class);
        $this->DataTable->addColumns([
            [5, 'falcons'],
            ['tacos', false],
        ]);
    }

    public function testAddRoleColumn(): void
    {
        $this->DataTable->addRoleColumn('number', 'interval');

        $column = $this->privateColumnAccess(0);

        $this->assertEquals('number', $column->getType());
        $this->assertEquals('interval', $column->getRole());
    }

    #[Depends('testAddRoleColumn')]
    #[DataProvider('nonStringProvider')]
    public function testAddRoleColumnWithBadColumnTypes($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnType::class);
        $this->DataTable->addRoleColumn($badTypes, 'interval');
    }

    #[Depends('testAddRoleColumn')]
    #[DataProvider('nonStringProvider')]
    public function testAddRoleColumnWithBadRoleTypes($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnRole::class);
        $this->DataTable->addRoleColumn('number', $badTypes);
    }

    #[Depends('testAddRoleColumn')]
    public function testAddRoleColumnWithBadRoleValue(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnRole::class);
        $this->DataTable->addRoleColumn('number', 'stairs');
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testDropColumnWithIndex(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addStringColumn();

        $columns = $this->privateColumnAccess();

        $this->assertEquals(3, count($columns));
        $this->assertEquals('number', $columns[1]->getType());

        $this->DataTable->dropColumn(1);
        $columns = $this->privateColumnAccess();
        $this->assertEquals(2, count($columns));
        $this->assertEquals('string', $columns[1]->getType());

        $this->DataTable->dropColumn(1);
        $columns = $this->privateColumnAccess();
        $this->assertEquals(1, count($columns));
        $this->assertFalse(isset($columns[1]));
    }

    #[Depends('testAddColumnViaNamedAlias')]
    #[DataProvider('nonIntProvider')]
    public function testDropColumnWithBadType($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnIndex::class);
        $this->DataTable->addNumberColumn();

        $this->DataTable->dropColumn($badTypes);
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testDropColumnWithNonExistentIndex(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnIndex::class);
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $this->DataTable->dropColumn(4);
    }

    #[Depends('testAddColumnByType')]
    #[DataProvider('columnTypeAndLabelProvider')]
    public function testAddColumnWithTypeAndLabel($columnType, $columnLabel): void
    {
        $this->DataTable->addColumn($columnType, $columnLabel);

        $column = $this->privateColumnAccess(0);

        $this->assertEquals($columnType, $column->getType());
        $this->assertEquals($columnLabel, $column->getLabel());
    }

    public function testAddColumnWithArrayOfTypeAndLabel(): void
    {
        $this->DataTable->addColumn(['date', 'Days in March']);

        $column = $this->privateColumnAccess(0);

        $this->assertEquals('date', $column->getType());
        $this->assertEquals('Days in March', $column->getLabel());
    }

    public function testAddColumnsWithArrayOfTypeAndLabel(): void
    {
        $this->DataTable->addColumns([
            ['date', 'Days in March'],
            ['number', 'Day of the Week'],
            ['number', 'Temperature'],
        ]);

        $columns = $this->privateColumnAccess();

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('Days in March', $columns[0]->getLabel());

        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('Day of the Week', $columns[1]->getLabel());

        $this->assertEquals('number', $columns[2]->getType());
        $this->assertEquals('Temperature', $columns[2]->getLabel());
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testAddRowWithEmptyArrayForNull(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addRow([]);

        $row = $this->privateRowAccess(0);

        $this->assertNull($this->inspect($row, 'values')[0]->getValue());
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testAddRowWithNull(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addRow(null);

        $row = $this->privateRowAccess(0);

        $this->assertNull($this->inspect($row, 'values')[0]->getValue());
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testAddRowWithDate(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addRow([Carbon::parse('March 24th, 1988')]);

        $column = $this->privateColumnAccess(0);
        $cell = $this->privateCellAccess(0, 0);

        $this->assertEquals('date', $column->getType());
        $this->assertInstanceOf('\Khill\Lavacharts\Datatables\Cells\DateCell', $cell);
        $this->assertEquals('Date(1988,2,24,0,0,0)', (string) $cell);
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testAddRowWithMultipleColumnsWithDateAndNumbers(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $this->DataTable->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);

        $columns = $this->privateColumnAccess();
        $row = $this->privateRowAccess(0);

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,0,0,0)', $row->getCell(0));
        $this->assertEquals(12345, $row->getCell(1)->getValue());
        $this->assertEquals(67890, $row->getCell(2)->getValue());
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testAddRowsWithMultipleColumnsWithDateAndNumbers(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344],
        ];

        $this->DataTable->addRows($rows);

        $columns = $this->privateColumnAccess();
        $rows = $this->privateRowAccess();

        $this->assertEquals('date', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,0,0,0)', (string) $rows[0]->getCell(0));
        $this->assertEquals(12345, $rows[0]->getCell(1)->getValue());
        $this->assertEquals(67890, $rows[0]->getCell(2)->getValue());

        $this->assertEquals('Date(1988,2,25,0,0,0)', (string) $rows[1]->getCell(0));
        $this->assertEquals(1122, $rows[1]->getCell(1)->getValue());
        $this->assertEquals(3344, $rows[1]->getCell(2)->getValue());
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testAddRowWithMoreCellsThanColumns(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidCellCount::class);
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();

        $this->DataTable->addRow([Carbon::parse('March 24th, 1988'), 12345, 67890]);
    }

    #[Depends('testAddColumnViaNamedAlias')]
    #[DataProvider('nonCarbonOrDateStringProvider')]
    public function testAddRowWithBadDateTypes($badDate): void
    {
        $this->expectException(\Exception::class);
        $this->DataTable->addDateColumn();

        $this->DataTable->addRow([$badDate]);
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testAddRowWithEmptyArray(): void
    {
        $this->DataTable->addDateColumn();
        $expectedRowCount = $this->DataTable->getRowCount();

        $this->DataTable->addRow([]);

        self::assertSame($expectedRowCount + 1, $this->DataTable->getRowCount());
    }

    #[Depends('testAddRowsWithMultipleColumnsWithDateAndNumbers')]
    public function testGetRows(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344],
        ];

        $this->DataTable->addRows($rows);

        $rows = $this->DataTable->getRows();

        $this->assertInstanceOf(DATATABLE_NS.'Rows\Row', $rows[0]);
        $this->assertInstanceOf(DATATABLE_NS.'Rows\Row', $rows[1]);
    }

    #[Depends('testGetRows')]
    public function testGetRowCount(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $rows = [
            [Carbon::parse('March 24th, 1988'), 12345, 67890],
            [Carbon::parse('March 25th, 1988'), 1122, 3344],
        ];

        $this->DataTable->addRows($rows);

        $this->assertEquals(2, $this->DataTable->getRowCount());
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testFormatColumn(): void
    {
        $mockDateFormat = \Mockery::mock(DATATABLE_NS.'Formats\DateFormat');

        $this->DataTable->addDateColumn();

        $this->DataTable->formatColumn(0, $mockDateFormat);

        $column = $this->privateColumnAccess(0);

        $this->assertInstanceOf(
            DATATABLE_NS.'Formats\DateFormat',
            $this->inspect($column, 'format')
        );
    }

    #[Depends('testAddColumnViaNamedAlias')]
    #[Depends('testFormatColumn')]
    public function testFormatColumnWithBadIndex(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidColumnIndex::class);
        $mockDateFormat = \Mockery::mock(DATATABLE_NS.'Formats\DateFormat');

        $this->DataTable->addDateColumn();

        $this->DataTable->formatColumn(672, $mockDateFormat);
    }

    #[Depends('testAddColumnViaNamedAlias')]
    #[Depends('testFormatColumn')]
    public function testFormatColumns(): void
    {
        $mockDateFormat = \Mockery::mock(DATATABLE_NS.'Formats\DateFormat');
        $mockNumberFormat = \Mockery::mock(DATATABLE_NS.'Formats\NumberFormat');

        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $this->DataTable->formatColumns([
            0 => $mockDateFormat,
            2 => $mockNumberFormat,
        ]);

        $columns = $this->privateColumnAccess();

        $this->assertInstanceOf(
            DATATABLE_NS.'Formats\DateFormat',
            $this->inspect($columns[0], 'format')
        );

        $this->assertInstanceOf(
            DATATABLE_NS.'Formats\NumberFormat',
            $this->inspect($columns[2], 'format')
        );
    }

    #[Depends('testAddColumnViaNamedAlias')]
    #[Depends('testFormatColumns')]
    public function testGetFormattedColumns(): void
    {
        $mockDateFormat = \Mockery::mock(DATATABLE_NS.'Formats\DateFormat');
        $mockNumberFormat = \Mockery::mock(DATATABLE_NS.'Formats\NumberFormat');

        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $this->DataTable->formatColumns([
            0 => $mockDateFormat,
            2 => $mockNumberFormat,
        ]);

        $columns = $this->DataTable->getFormattedColumns();

        $this->assertInstanceOf(
            DATATABLE_NS.'Formats\DateFormat',
            $this->inspect($columns[0], 'format')
        );

        $this->assertInstanceOf(
            DATATABLE_NS.'Formats\NumberFormat',
            $this->inspect($columns[2], 'format')
        );
    }

    #[Depends('testAddColumnViaNamedAlias')]
    #[Depends('testGetFormattedColumns')]
    public function testHasFormattedColumns(): void
    {
        $mockDateFormat = \Mockery::mock(DATATABLE_NS.'Formats\DateFormat');
        $mockNumberFormat = \Mockery::mock(DATATABLE_NS.'Formats\NumberFormat');

        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $this->DataTable->formatColumns([
            0 => $mockDateFormat,
            2 => $mockNumberFormat,
        ]);

        $this->assertTrue($this->DataTable->hasFormattedColumns());
    }

    #[Depends('testAddColumnViaNamedAlias')]
    #[Depends('testGetFormattedColumns')]
    public function testHasFormattedColumnsWithNoFormattedColumns(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $this->assertFalse($this->DataTable->hasFormattedColumns());
    }

    #[Depends('testAddColumnWithTypeAndLabel')]
    public function testAddRowsWithMultipleColumnsWithDateTimeAndNumbers(): void
    {
        $this->DataTable->addColumns([
            ['datetime'],
            ['number'],
            ['number'],
        ])->addRows([
            [Carbon::parse('March 24th, 1988 8:01:05'), 12345, 67890],
            [Carbon::parse('March 25th, 1988 8:02:06'), 1122, 3344],
        ]);

        $columns = $this->privateColumnAccess();
        $rows = $this->privateRowAccess();

        $this->assertEquals('datetime', $columns[0]->getType());
        $this->assertEquals('number', $columns[1]->getType());
        $this->assertEquals('number', $columns[2]->getType());

        $this->assertEquals('Date(1988,2,24,8,1,5)', $rows[0]->getCell(0));
        $this->assertEquals(12345, $rows[0]->getCell(1)->getValue());
        $this->assertEquals(67890, $rows[0]->getCell(2)->getValue());

        $this->assertEquals('Date(1988,2,25,8,2,6)', $rows[1]->getCell(0));
        $this->assertEquals(1122, $rows[1]->getCell(1)->getValue());
        $this->assertEquals(3344, $rows[1]->getCell(2)->getValue());
    }

    #[Depends('testAddColumnWithTypeAndLabel')]
    public function testGetColumn(): void
    {
        $this->DataTable->addColumn('date', 'Test1');
        $this->DataTable->addColumn('number', 'Test2');

        $column = $this->DataTable->getColumn(1);

        $this->assertInstanceOf(DATATABLE_NS.'Columns\Column', $column);
        $this->assertEquals('Test2', $this->inspect($column, 'label'));
    }

    #[Depends('testAddColumnWithTypeAndLabel')]
    public function testGetColumns(): void
    {
        $this->DataTable->addColumn('date', 'Test1');
        $this->DataTable->addColumn('number', 'Test2');

        $columns = $this->DataTable->getColumns();

        $this->assertTrue(is_array($columns));
        $this->assertInstanceOf(DATATABLE_NS.'Columns\Column', $columns[0]);
        $this->assertInstanceOf(DATATABLE_NS.'Columns\Column', $columns[1]);
    }

    #[Depends('testAddColumnWithTypeAndLabel')]
    public function testGetColumnLabel(): void
    {
        $this->DataTable->addColumn('date', 'Test1');
        $this->DataTable->addColumn('number', 'Test2');

        $this->assertEquals('Test2', $this->DataTable->getColumnLabel(1));
    }

    #[Depends('testAddColumnWithTypeAndLabel')]
    public function testGetColumnLabels(): void
    {
        $this->DataTable->addColumn('date', 'Test1');
        $this->DataTable->addColumn('number', 'Test2');

        $labels = $this->DataTable->getColumnLabels();

        $this->assertTrue(is_array($labels));
        $this->assertEquals('Test1', $labels[0]);
        $this->assertEquals('Test2', $labels[1]);
    }

    #[Depends('testAddColumnWithTypeAndLabel')]
    public function testGetColumnType(): void
    {
        $this->DataTable->addColumn('date', 'Test1');
        $this->DataTable->addColumn('number', 'Test2');

        $this->assertEquals('date', $this->DataTable->getColumnType(0));
    }

    #[Depends('testAddColumnByType')]
    #[DataProvider('columnTypeProvider')]
    public function testGetColumnTypeWithIndex($type): void
    {
        $this->DataTable->addColumn($type);

        $this->assertEquals($type, $this->DataTable->getColumnType(0));
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testGetColumnsByType(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();

        $this->assertEquals([0], array_keys($this->DataTable->getColumnsByType('date')));
        $this->assertEquals([1], array_keys($this->DataTable->getColumnsByType('number')));
    }

    #[Depends('testAddColumnViaNamedAlias')]
    public function testGetColumnsByTypeWithDuplicateTypes(): void
    {
        $this->DataTable->addDateColumn();
        $this->DataTable->addNumberColumn();
        $this->DataTable->addNumberColumn();

        $this->assertTrue(is_array($this->DataTable->getColumnsByType('number')));
        $this->assertEquals([1, 2], array_keys($this->DataTable->getColumnsByType('number')));
    }

    #[Depends('testAddColumnByType')]
    #[Depends('testAddRowsWithMultipleColumnsWithDateAndNumbers')]
    #[Depends('testGetRowCount')]
    public function testBare(): void
    {
        $this->DataTable->addNumberColumn('num')->addRows([[1], [2], [3], [4]]);

        $this->assertEquals($this->DataTable->getRowCount(), 4);

        $this->assertEquals($this->DataTable->bare()->getRowCount(), 0);
    }
}
