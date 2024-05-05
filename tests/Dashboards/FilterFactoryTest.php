<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Filters\FilterFactory;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

class FilterFactoryTest extends ProvidersTestCase
{
    public static function filterTypeProvider(): array
    {
        return [
            ['Category'],
            ['ChartRange'],
            ['DateRange'],
            ['NumberRange'],
            ['String']
        ];
    }

    #[DataProvider('filterTypeProvider')]
    public function testStaticCreateMethodWithColumnIndex($filterType): void
    {
        $filterClass = FilterFactory::create($filterType, 2);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(2, $options['filterColumnIndex']);
    }

    #[DataProvider('filterTypeProvider')]
    public function testStaticCreateMethodWithColumnLabel($filterType): void
    {
        $filterClass = FilterFactory::create($filterType, 'myColumnLabel');

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals('myColumnLabel', $options['filterColumnLabel']);
    }

    #[Depends('testStaticCreateMethodWithColumnLabel')]
    #[DataProvider('filterTypeProvider')]
    public function testStaticCreateMethodWithColumnLabelAndOptions($filterType): void
    {
        $filterClass = FilterFactory::create($filterType, 'myColumnLabel', ['floatOption' => 12.34]);

        $options = $this->inspect($filterClass, 'options');

        $this->assertEquals(12.34, $options['floatOption']);
    }

    #[DataProvider('nonStringProvider')]
    public function testStaticCreateMethodWithInvalidType($badType): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidFilterType::class);
        FilterFactory::create($badType, 1);
    }

    #[DataProvider('nonStringOrIntProvider')]
    public function testStaticCreateMethodWithInvalidIndex($badType): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidParamType::class);
        FilterFactory::create('String', $badType);
    }
}
