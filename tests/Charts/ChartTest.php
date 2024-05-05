<?php

namespace Khill\Lavacharts\Tests\Charts;

use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

class ChartTest extends ProvidersTestCase
{
    public function makeLineChart($options = []): LineChart
    {
        return new LineChart(
            $this->getMockLabel('TestChart'),
            $this->getMockDataTable(),
            $options
        );
    }

    #[DataProvider('chartTypeProvider')]
    public function testInstanceCreation($chartType): void
    {
        $chartFQN = "Khill\\Lavacharts\\Charts\\".$chartType;

        $chart = new $chartFQN(
            $this->getMockLabel('TestChart'),
            $this->getMockDataTable()
        );

        $this->assertEquals('TestChart', $chart->getLabelStr());
        $this->assertEquals($chartType, $chart->getType());
        $this->assertInstanceOf(DATATABLE_NS.'DataTable', $chart->getDataTable());
    }

    #[Depends('testInstanceCreation')]
    public function testSettingOptionsWithConstructor(): void
    {
        $chart = $this->makeLineChart(
            ['colors' => ['red', 'green']]
        );

        $options = $this->inspect($chart, 'options');

        $this->assertTrue(is_array($options));
        $this->assertArrayHasKey('colors', $options);
        $this->assertEquals(['red', 'green'], $options['colors']);
    }

    #[Depends('testSettingOptionsWithConstructor')]
    public function testGetOptions(): void
    {
        $chart = $this->makeLineChart(
            ['colors' => ['red', 'green']]
        );

        $options = $chart->getOptions();

        $this->assertTrue(is_array($options));
        $this->assertEquals(['red', 'green'], $options['colors']);
    }

    #[Depends('testGetOptions')]
    public function testSettingOptionsViaMagicMethod(): void
    {
        $chart = $this->makeLineChart();

        $chart->legend(['position' => 'out']);

        $options = $chart->getOptions();

        $this->assertArrayHasKey('legend', $options);
        $this->assertArrayHasKey('position', $options['legend']);
        $this->assertEquals('out', $options['legend']['position']);
    }

    #[Depends('testGetOptions')]
    public function testSettingStringValueOptionViaMagicMethod(): void
    {
        $chart = $this->makeLineChart();

        $chart->title('Charts!');

        $options = $chart->getOptions();

        $this->assertEquals('Charts!', $options['title']);
    }

    #[Depends('testGetOptions')]
    public function testSetOptions(): void
    {
        $expected = [
            'title' => 'My Cool Chart',
            'width' => 1024,
            'height' => 768
        ];

        $chart = $this->makeLineChart();
        $chart->setOptions($expected);

        $options = $chart->getOptions();

        $this->assertTrue(is_array($options));
        $this->assertEquals('My Cool Chart', $options['title']);
        $this->assertEquals(1024, $options['width']);
        $this->assertEquals(768, $options['height']);
    }

    #[Depends('testSetOptions')]
    #[Depends('testGetOptions')]
    public function testMergeOptions(): void
    {
        $expected = [
            'title' => 'My Cool Chart'
        ];

        $chart = $this->makeLineChart();

        $chart->setOptions($expected);

        $chart->mergeOptions(['width' => 1024]);

        $options = $chart->getOptions();

        $this->assertEquals('My Cool Chart', $options['title']);
        $this->assertEquals(1024, $options['width']);
    }

    #[Depends('testSetOptions')]
    #[Depends('testGetOptions')]
    public function testCustomize(): void
    {
        $expected = [
            'title' => 'My Cool Chart',
            'width' => 1024,
            'height' => 768
        ];

        $chart = $this->makeLineChart();
        $chart->customize($expected);

        $options = $chart->getOptions();

        $this->assertEquals('My Cool Chart', $options['title']);
        $this->assertEquals(1024, $options['width']);
        $this->assertEquals(768, $options['height']);
    }

    #[Depends('testSettingOptionsViaMagicMethod')]
    public function testOptionsToJson(): void
    {
        $chart = $this->makeLineChart();

        $chart->title('My Cool Chart');
        $chart->width(1024);
        $chart->height(768);

        $expected = '{"title":"My Cool Chart","width":1024,"height":768}';

        $this->assertEquals($expected, json_encode($chart));
    }
}
