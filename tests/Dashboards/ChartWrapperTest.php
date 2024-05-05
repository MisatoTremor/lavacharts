<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Wrappers\Wrapper;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Depends;

#[CoversMethod(Wrapper::class, 'getElementId')]
#[CoversMethod(Wrapper::class, 'unwrap')]
#[CoversMethod(Wrapper::class, 'getJsClass')]
class ChartWrapperTest extends ProvidersTestCase
{
    public $mockElementId;
    public $jsonOutput;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockElementId = $this->getMockElementId('TestLabel');

        $this->jsonOutput = '{"options":{"Option1":5,"Option2":true},"containerId":"TestLabel","chartType":"LineChart"}';
    }

    public function getMockLineChart()
    {
        return \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')
            ->shouldReceive('setRenderable')
            ->once()
            ->with(false)
            ->shouldReceive('getType')
            ->once()
            ->andReturn('LineChart')
            ->shouldReceive('getWrapType')
            ->once()
            ->andReturn('chartType')
            ->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();
    }

    public function testGetElementId(): void
    {
        $areaChart = \Mockery::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial();

        $chartWrapper = new ChartWrapper($areaChart, $this->mockElementId);

        $this->assertInstanceOf('\Khill\Lavacharts\Values\ElementId', $chartWrapper->getElementId());
        $this->assertEquals('TestLabel', $chartWrapper->getElementIdStr());
    }

    public function testUnwrap(): void
    {
        $areaChart = \Mockery::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial();

        $chartWrapper = new ChartWrapper($areaChart, $this->mockElementId);

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $chartWrapper->unwrap());
    }

    public function testGetJsClass(): void
    {
        $chart = \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')
            ->shouldReceive('setRenderable')
            ->once()
            ->with(false)
            ->getMock();

        $chartWrapper = new ChartWrapper($chart, $this->mockElementId);

        $javascript = 'google.visualization.ChartWrapper';

        $this->assertEquals($javascript, $chartWrapper->getJsClass());
    }

    public function testJsonSerialize(): void
    {
        $chart = $this->getMockLineChart();

        $chartWrapper = new ChartWrapper($chart, $this->mockElementId);

        $this->assertEquals($this->jsonOutput, json_encode($chartWrapper));
    }

    #[Depends('testJsonSerialize')]
    public function testToJson(): void
    {
        $chart = $this->getMockLineChart();

        $chartWrapper = new ChartWrapper($chart, $this->mockElementId);

        $this->assertEquals($this->jsonOutput, $chartWrapper->toJson());
    }

    #[Depends('testGetJsClass')]
    #[Depends('testToJson')]
    public function testGetJsConstructor(): void
    {
        $chart = $this->getMockLineChart();

        $chartWrapper = new ChartWrapper($chart, $this->mockElementId);

        $this->assertEquals(
            'new google.visualization.ChartWrapper('.$this->jsonOutput.')',
            $chartWrapper->getJsConstructor()
        );
    }
}
