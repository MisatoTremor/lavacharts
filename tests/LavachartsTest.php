<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Charts\ChartFactory;
use Khill\Lavacharts\Lavacharts;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use Khill\Lavacharts\Charts\PieChart;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\DataTables\DataTable;
use Khill\Lavacharts\Values\Label;

class LavachartsTest extends ProvidersTestCase
{
    private Lavacharts $lava;

    public function setUp(): void
    {
        parent::setUp();

        $this->lava = new Lavacharts;

        $this->mockLabel = \Mockery::mock(Label::class, ['MockLabel'])->makePartial();

        $this->mockLineChart = \Mockery::mock(LineChart::class);
    }

    public function testCreateDataTableViaAlias(): void
    {
        $this->assertInstanceOf(DATATABLE_NS.'DataTable', $this->lava->DataTable());
    }

    public function testCreateDataTableViaAliasWithTimezone(): void
    {
        $this->assertInstanceOf(DATATABLE_NS.'DataTable', $this->lava->DataTable('America/Los_Angeles'));
    }

    public function testExistsWithExistingChartInVolcano(): void
    {
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertTrue($this->lava->exists('LineChart', 'TestChart'));
    }

    public function testExistsWithNonExistentChartTypeInVolcano(): void
    {
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertFalse($this->lava->exists('SheepChart', 'TestChart'));
    }

    public function testExistsWithNonExistentChartLabelInVolcano(): void
    {
        $this->lava->LineChart('WhaaaaatChart?', $this->partialDataTable);

        $this->assertFalse($this->lava->exists('LineChart', 'TestChart'));
    }

    #[DataProvider('nonStringProvider')]
    public function testExistsWithNonStringInputForType($badTypes): void
    {
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertFalse($this->lava->exists($badTypes, 'TestChart'));
    }

    #[DataProvider('nonStringProvider')]
    public function testExistsWithNonStringInputForLabel($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidLabel::class);
        $this->lava->LineChart('TestChart', $this->partialDataTable);

        $this->assertFalse($this->lava->exists('LineChart', $badTypes));
    }


    #[DataProvider('chartTypeProvider')]
    public function testCreatingChartsViaMagicMethodOfLavaObject($chartType): void
    {
        $chart = $this->lava->$chartType(
            'My Fancy '.$chartType,
            $this->getMockDataTable()
        );

        $this->assertEquals('My Fancy '.$chartType, $chart->getLabelStr());
        $this->assertEquals($chartType, $chart->getType());
        $this->assertInstanceOf(DATATABLE_NS.'DataTable', $chart->getDataTable());
    }

    #[Depends('testCreateDataTableViaAlias')]
    public function testRenderChart(): void
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div')));
    }

    #[Depends('testCreateDataTableViaAlias')]
    public function testRenderChartWithElementIdAndDivWithNoDimensions(): void
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $output = $this->lava->render('LineChart', 'test', 'test-div', true);

        $this->assertStringHasString($output, '<div id="test-div"></div>');
    }

    #[Depends('testCreateDataTableViaAlias')]
    public function testUseChartElementIdWhenMissingInRenderCall(): void
    {
        $this->lava->LineChart('test', $this->partialDataTable, [
            'elementId' => 'test-div'
        ]);

        $output = $this->lava->render('LineChart', 'test');

        $this->assertStringHasString($output, '$chart.setElement(\'test-div\')');
    }

    #[Depends('testCreateDataTableViaAlias')]
    public function testRenderChartWithDivAndDimensions(): void
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $dims = [
            'height' => 200,
            'width' => 200
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    #[Depends('testCreateDataTableViaAlias')]
    public function testRenderChartWithDivAndBadDimensionKeys(): void
    {
        $this->lava->LineChart('test', $this->partialDataTable);

        $dims = [
            'heiXght' => 200,
            'wZidth' => 200
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', $dims)));
    }

    #[Depends('testCreateDataTableViaAlias')]
    public function testRenderChartWithDivAndBadDimensionType(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidDivDimensions::class);
        $this->lava->LineChart('test', $this->partialDataTable);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div', 'TacosTacosTacos')));
    }

    #[Depends('testCreateDataTableViaAlias')]
    public function testRenderChartWithDivAndDimensionsWithBadValues(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidConfigValue::class);
        $this->lava->LineChart('my-chart', $this->partialDataTable);

        $dims = [
            'height' => 4.6,
            'width' => 'hotdogs'
        ];

        $this->assertTrue(is_string($this->lava->render('LineChart', 'my-chart', 'test-div', $dims)));
    }

    #[Depends('testCreateDataTableViaAlias')]
    public function testCreateFormatObjectViaAliasWithConstructorConfig(): void
    {
        $dt = $this->lava->DataTable();

        $df = $this->lava->DateFormat([
            'formatType' => 'medium'
        ]);

        $dt->addDateColumn('dates', $df);

        $this->lava->LineChart('test', $dt);

        $this->assertTrue(is_string($this->lava->render('LineChart', 'test', 'test-div')));
    }

    public function testRenderAliasWithInvalidLavaObject(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidLavaObject::class);
        $this->lava->renderTacoChart();
    }

    public function testCreateChartWithMissingLabel(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidLabel::class);
        $this->lava->LineChart();
    }

    public function testCreateChartWithInvalidLabel(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidLabel::class);
        $this->lava->LineChart(5, $this->partialDataTable);
    }

    #[Depends('testCreatingChartsViaMagicMethodOfLavaObject')]
    public function testStoreChartIntoVolcano(): void
    {
        $mockPieChart = \Mockery::mock(PieChart::class, [
            $this->mockLabel,
            $this->getMockDataTable()
        ])->shouldReceive('getType')
          ->andReturn('PieChart')
          ->shouldReceive('getLabel')
          ->andReturn('MockLabel')
          ->getMock();

        $this->lava->store($mockPieChart);

        $volcano = $this->inspect($this->lava, 'volcano');
        $charts = $this->inspect($volcano, 'charts');

        $this->assertArrayHasKey('PieChart', $charts);
        $this->assertInstanceOf(PieChart::class, $charts['PieChart']['MockLabel']);
    }

    public function testJsapiMethodWithCoreJsTracking(): void
    {
        $this->lava->jsapi();

        $this->assertTrue(
            $this->inspect($this->lava, 'scriptManager')->lavaJsRendered()
        );
    }

    public function testLavaJsMethodWithCoreJsTracking(): void
    {
        $this->lava->lavajs();

        $this->assertTrue(
            $this->inspect($this->lava, 'scriptManager')->lavaJsRendered()
        );
    }

    public function formatTypeProvider(): array
    {
        return [
            ['ArrowFormat'],
            ['BarFormat'],
            ['DateFormat'],
            ['NumberFormat']
        ];
    }
}
