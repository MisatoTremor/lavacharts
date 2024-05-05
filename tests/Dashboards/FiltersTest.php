<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Filters\Filter;
use Khill\Lavacharts\Dashboards\Filters\StringFilter;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

#[CoversMethod(Filter::class, 'getType')]
class FiltersTest extends ProvidersTestCase
{
    public static function filterTypeProvider(): array
    {
        return [
            ['CategoryFilter'],
            ['ChartRangeFilter'],
            ['DateRangeFilter'],
            ['NumberRangeFilter'],
            ['StringFilter']
        ];
    }

    #[DataProvider('filterTypeProvider')]
    public function testConstructorWithColumnIndex($filterType): void
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter(2);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(2, $options['filterColumnIndex']);
    }

    #[DataProvider('filterTypeProvider')]
    public function testConstructorWithColumnLabel($filterType): void
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter('myColumnLabel');

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals('myColumnLabel', $options['filterColumnLabel']);
    }

    #[Depends('testConstructorWithColumnIndex')]
    #[DataProvider('filterTypeProvider')]
    public function testConstructorWithColumnIndexAndOptions($filterType): void
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter(2, ['floatOption' => 12.34]);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(12.34, $options['floatOption']);
    }

    #[Depends('testConstructorWithColumnLabel')]
    #[DataProvider('filterTypeProvider')]
    public function testGetWrapType($filterType): void
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter('myColumnLabel');

        $this->assertEquals('controlType', $filterClass->getWrapType());
    }

    #[Depends('testConstructorWithColumnIndex')]
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidParamType::class);
        new StringFilter(new \stdClass());
    }

    #[Depends('testConstructorWithColumnIndex')]
    #[DataProvider('filterTypeProvider')]
    public function testGetType($filterType): void
    {
        $filter = 'Khill\Lavacharts\Dashboards\Filters\\'.$filterType;

        $filterClass = new $filter('myColumnLabel');

        $this->assertEquals($filterType, $filterClass->getType());
    }
}
