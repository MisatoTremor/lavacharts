<?php

namespace Khill\Lavacharts\Tests;

use Khill\Lavacharts\Volcano;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;

/**
 * @property \Mockery\MockInterface mockDashboard
 * @property \Mockery\MockInterface mockLineChart
 * @property \Mockery\Mock          mockBadLabel
 * @property \Mockery\Mock          mockGoodLabel
 */
#[\AllowDynamicProperties]
class VolcanoTest extends ProvidersTestCase
{
    public Volcano $volcano;

    public function setUp(): void
    {
        parent::setUp();

        $this->volcano = new Volcano;

        $this->mockGoodLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestRenderable'])->makePartial();

        $this->mockBadLabel = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['Pumpkins'])->makePartial();

        $this->mockLineChart = \Mockery::mock('\Khill\Lavacharts\Charts\LineChart', [
            $this->mockGoodLabel,
            $this->getMockDataTable(),
        ])->shouldReceive('getLabel')
            ->andReturn('TestRenderable')
            ->shouldReceive('getType')
            ->zeroOrMoreTimes()
            ->andReturn('LineChart')
            ->getMock();

        $this->mockDashboard = \Mockery::mock('\Khill\Lavacharts\Dashboards\Dashboard', [
            $this->mockGoodLabel,
            $this->getMockDataTable(),
        ])->shouldReceive('getLabel')->andReturn('TestRenderable')->getMock();
    }

    #[Group('chart')]
    public function testStoreWithChart(): void
    {
        $this->volcano->store($this->mockLineChart);

        $volcanoCharts = $this->inspect($this->volcano, 'charts');

        $chart = $volcanoCharts['LineChart']['TestRenderable'];

        $this->assertInstanceOf(self::NS.'\Charts\Chart', $chart);
    }

    #[Group('dash')]
    public function testStoreWithDashboard(): void
    {
        $chart = \Mockery::mock('\Khill\Lavacharts\Dashboards\Dashboard', [
            $this->mockGoodLabel,
            $this->getMockDataTable(),
        ])->shouldReceive('getLabel')
            ->andReturn('TestRenderable')
            ->getMock();

        $this->assertEquals($this->volcano->store($chart), $chart);
    }

    #[Group('chart')]
    #[Depends('testStoreWithChart')]
    public function testCheckChart(): void
    {
        $this->volcano->store($this->mockLineChart);

        $this->assertTrue($this->volcano->checkChart('LineChart', $this->mockGoodLabel));

        $this->assertFalse($this->volcano->checkChart('LaserChart', $this->mockGoodLabel));
        $this->assertFalse($this->volcano->checkChart('LineChart', $this->mockBadLabel));
    }

    #[Group('chart')]
    #[Depends('testStoreWithChart')]
    #[Depends('testCheckChart')]
    public function testGetChart(): void
    {
        $this->volcano->store($this->mockLineChart);

        $this->assertInstanceOf(
            '\Khill\Lavacharts\Charts\LineChart',
            $this->volcano->get('LineChart', $this->mockGoodLabel)
        );
    }

    #[Group('chart')]
    #[Depends('testStoreWithChart')]
    #[Depends('testCheckChart')]
    #[Depends('testGetChart')]
    public function testGetChartWithBadChartType(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\ChartNotFound::class);
        $this->volcano->store($this->mockLineChart);
        $this->volcano->get('LaserChart', $this->mockGoodLabel);
    }

    #[Group('chart')]
    #[Depends('testStoreWithChart')]
    #[Depends('testCheckChart')]
    #[Depends('testGetChart')]
    public function testGetChartWithNonExistentLabel(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\ChartNotFound::class);
        $this->volcano->store($this->mockLineChart);
        $this->volcano->get('LineChart', $this->mockBadLabel);
    }

    #[Group('dashboard')]
    public function testStoreDashboard(): void
    {
        $this->assertEquals($this->volcano->store($this->mockDashboard), $this->mockDashboard);
    }

    #[Group('dashboard')]
    #[Depends('testStoreDashboard')]
    public function testCheckDashboard(): void
    {
        $this->volcano->store($this->mockDashboard);

        $this->assertTrue($this->volcano->checkDashboard($this->mockGoodLabel));
    }

    #[Group('dashboard')]
    #[Depends('testStoreDashboard')]
    #[Depends('testCheckDashboard')]
    public function testGetDashboard(): void
    {
        $this->volcano->store($this->mockDashboard);

        $dash = $this->volcano->get('Dashboard', $this->mockGoodLabel);

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Dashboard', $dash);
    }

    #[Group('dashboard')]
    #[Depends('testStoreWithDashboard')]
    #[Depends('testCheckDashboard')]
    #[Depends('testGetDashboard')]
    public function testGetDashboardWithBadLabel(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\DashboardNotFound::class);
        $this->volcano->store($this->mockDashboard);

        $this->volcano->get('Dashboard', $this->mockBadLabel);
    }

    #[Group('chart')]
    #[Group('dashboard')]
    #[Depends('testGetChart')]
    #[Depends('testGetDashboard')]
    public function testGetAll(): void
    {
        $this->volcano->store($this->mockLineChart);
        $this->volcano->store($this->mockDashboard);

        foreach ($this->volcano->getAll() as $renderable) {
            $this->assertInstanceOf('\Khill\Lavacharts\Support\Contracts\RenderableInterface', $renderable);
        }
    }
}
