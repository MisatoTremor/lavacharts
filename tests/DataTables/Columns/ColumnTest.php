<?php

namespace Khill\Lavacharts\Tests\DataTables\Columns;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Columns\Column;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Depends;

/**
 * @property \Mockery\Mock mockRole
 * @property \Mockery\Mock mockFormat
 */
#[CoversMethod(Column::class, '__construct')]
#[CoversMethod(Column::class, 'getType')]
#[CoversMethod(Column::class, 'getLabel')]
#[CoversMethod(Column::class, 'isFormatted')]
#[CoversMethod(Column::class, 'getFormat')]
#[CoversMethod(Column::class, 'getRole')]
#[CoversMethod(Column::class, 'jsonSerialize')]
#[\AllowDynamicProperties]
class ColumnTest extends ProvidersTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->mockRole   = \Mockery::mock('\Khill\Lavacharts\Values\Role')
                                    ->shouldReceive('__toString')
                                    ->zeroOrMoreTimes()
                                    ->andReturn('interval')
                                    ->getMock();

        $this->mockFormat = \Mockery::mock('\Khill\Lavacharts\DataTables\Formats\NumberFormat')->makePartial();
    }

    public function testConstructorWithType(): void
    {
        $column = new Column('number');

        $this->assertEquals('number', $this->inspect($column, 'type'));
    }

    #[Depends('testConstructorWithType')]
    public function testConstructorWithTypeAndLabel(): void
    {
        $column = new Column('number', 'MyLabel');

        $this->assertEquals('MyLabel', $this->inspect($column, 'label'));
    }

    #[Depends('testConstructorWithTypeAndLabel')]
    public function testConstructorWithTypeAndLabelAndFormat(): void
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\NumberFormat', $this->inspect($column, 'format'));
    }

    #[Depends('testConstructorWithTypeAndLabelAndFormat')]
    public function testConstructorWithTypeAndLabelAndFormatAndRole(): void
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat, $this->mockRole);

        $this->assertInstanceOf('\Khill\Lavacharts\Values\Role', $this->inspect($column, 'role'));
    }

    #[Depends('testConstructorWithType')]
    public function testGetType(): void
    {
        $column = new Column('number');

        $this->assertEquals('number', $column->getType());
    }

    #[Depends('testConstructorWithTypeAndLabel')]
    public function testGetLabel(): void
    {
        $column = new Column('number', 'MyLabel');

        $this->assertEquals('MyLabel', $column->getLabel());
    }

    #[Depends('testConstructorWithTypeAndLabelAndFormat')]
    public function testIsFormatted(): void
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat);

        $this->assertTrue($column->isFormatted());
    }

    public function testGetFormat(): void
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\Formats\NumberFormat', $column->getFormat());
    }

    public function testGetRole(): void
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat, $this->mockRole);

        $this->assertEquals('interval', $column->getRole());
    }

    #[Depends('testConstructorWithTypeAndLabelAndFormatAndRole')]
    public function testJsonSerialization(): void
    {
        $column = new Column('number', 'MyLabel', $this->mockFormat, $this->mockRole);

        $json = '{"type":"number","label":"MyLabel","p":{"role":"interval"}}';

        $this->assertEquals($json, json_encode($column));
    }
}
