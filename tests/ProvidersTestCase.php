<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\DataTables\Columns\ColumnFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

define('DATATABLE_NS', "\\Khill\\Lavacharts\\DataTables\\");

abstract class ProvidersTestCase extends MockeryTestCase
{
    /**
     * Namespace for Mocks
     */
    const NS = '\Khill\Lavacharts';

    /**
     * Partial DataTable for use throughout various tests
     *
     * @var \Khill\Lavacharts\DataTables\DataTable
     */
    protected $partialDataTable;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * Setting timezone to avoid warning from Carbon
         */
        date_default_timezone_set('America/Los_Angeles');

        $this->partialDataTable = \Mockery::mock(DATATABLE_NS.'DataTable')->makePartial();
    }

    /**
     * Checks if a string contains another string
     *
     * @param $haystack
     * @param $needle
     */
    public function assertStringHasString($haystack, $needle): void
    {
        $this->assertTrue(strpos($haystack, $needle) !== false);
    }

    /**
     * Uses reflection to retrieve private member variables from objects.
     *
     * @param  object $obj
     * @param  string $prop
     * @return mixed
     */
    public function inspect($obj, $prop): mixed
    {
        $refObj = new \ReflectionProperty($obj, $prop);

        return $refObj->getValue($obj);
    }

    /**
     * DataProvider for the column types
     *
     * @return array
     */
    public static function columnTypeProvider(): array
    {
        return array_map(function ($columnType) {
            return [$columnType];
        }, ColumnFactory::$types);
    }

    /**
     * DataProvider for the chart types
     *
     * @return array
     */
    public static function chartTypeProvider(): array
    {
        return array_map(function ($chartType) {
            return [$chartType];
        }, ChartFactory::getChartTypes());
    }

    /**
     * Create a mock Label with the given string
     *
     * @param  string $label
     * @return \Mockery\Mock
     */
    public function getMockLabel($label): object
    {
        return \Mockery::mock('\Khill\Lavacharts\Values\Label', [$label])->makePartial();
    }

    /**
     * Create a mock ElementId with the given string
     *
     * @param  string $label
     * @return \Mockery\Mock
     */
    public function getMockElementId($label): object
    {
        return \Mockery::mock('\Khill\Lavacharts\Values\ElementId', [$label])->makePartial();
    }

    /**
     * Create a mock DataTable
     *
     * @return \Mockery\Mock
     */
    public function getMockDataTable(): object
    {
        return \Mockery::mock('Khill\Lavacharts\DataTables\DataTable')->makePartial();
    }

    public static function nonStringOrIntProvider(): array
    {
        return [
            [3.2],
            [true],
            [false],
            [[]],
            [new \stdClass]
        ];
    }

    public static function nonIntOrPercentProvider(): array
    {
        return [
            [3.2],
            [true],
            [false],
            [[]],
            ['notapercent'],
            [new \stdClass]
        ];
    }

    public static function nonCarbonOrDateStringProvider(): array
    {
        return [
            [9],
            [14.6342],
            [true],
            [false],
            [new \stdClass()]
        ];
    }

    public static function nonCarbonOrDateOrEmptyArrayProvider(): array
    {
        return [
            ['cheese'],
            [9],
            [14.6342],
            [true],
            [false],
            [new \stdClass()]
        ];
    }

    public static function nonStringOrNullProvider(): array
    {
        return [
            [9],
            [1.2],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public static function nonStringProvider(): array
    {
        return [
            [9],
            [1.2],
            [true],
            [false],
            [null],
            [[]],
            [new \stdClass()]
        ];
    }

    public static function nonBoolProvider(): array
    {
        return [
            ['Imastring'],
            [9],
            [1.2],
            [[]],
            [new \stdClass()]
        ];
    }

    public static function nonIntProvider(): array
    {
        return [
            ['Imastring'],
            [1.2],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public static function nonFloatProvider(): array
    {
        return [
            ['Imastring'],
            [9],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public static function nonNumericProvider(): array
    {
        return [
            ['Imastring'],
            [true],
            [false],
            [[]],
            [new \stdClass()]
        ];
    }

    public static function nonArrayProvider(): array
    {
        return [
            ['Imastring'],
            [9],
            [1.2],
            [true],
            [false],
            [new \stdClass()]
        ];
    }
}
