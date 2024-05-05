<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Bindings\Binding;
use Khill\Lavacharts\Dashboards\Bindings\BindingFactory;
use Khill\Lavacharts\Dashboards\Bindings\ManyToMany;
use Khill\Lavacharts\Dashboards\Bindings\ManyToOne;
use Khill\Lavacharts\Dashboards\Bindings\OneToMany;
use Khill\Lavacharts\Dashboards\Bindings\OneToOne;
use Khill\Lavacharts\Dashboards\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Depends;

#[CoversClass(OneToOne::class)]
#[CoversClass(OneToMany::class)]
#[CoversClass(ManyToOne::class)]
#[CoversClass(ManyToMany::class)]
#[CoversClass(Binding::class)]
#[CoversMethod(BindingFactory::class, 'create')]
#[CoversMethod(Dashboard::class, 'bind')]
#[CoversMethod(Dashboard::class, 'getBindings')]
#[CoversMethod(Dashboard::class, 'setBindings')]
#[CoversMethod(Dashboard::class, 'getBoundCharts')]
class DashboardTest extends DashboardsTestCase
{
    private Dashboard $dashboard;

    public function setUp(): void
    {
        parent::setUp();

        $this->dashboard = new Dashboard(
            \Mockery::mock('\Khill\Lavacharts\Values\Label', ['myDash'])->makePartial(),
            $this->partialDataTable,
            \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['my-dash'])->makePartial()
        );
    }

    public function testBindingFactoryWithBadTypes(): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidBindings::class);
        $this->dashboard->bind(612345, 'tacos');
        $this->dashboard->bind(61.345, []);
        $this->dashboard->bind([], false);
    }

    public function testGetBindings(): void
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        $bindings = $this->dashboard->getBindings();

        $this->assertTrue(is_array($bindings));
    }

    #[Depends('testGetBindings')]
    public function testBindWithOneToOne(): void
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $binding);
    }

    #[Depends('testGetBindings')]
    public function testBindWithOneToMany(): void
    {
        $this->dashboard->bind(
            $this->mockControlWrap,
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToMany', $binding);
    }

    #[Depends('testGetBindings')]
    public function testBindWithManyToOne(): void
    {
        $this->dashboard->bind(
            [$this->mockControlWrap, $this->mockControlWrap],
            $this->mockChartWrap
        );

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\ManyToOne', $binding);
    }

    #[Depends('testGetBindings')]
    public function testBindWithManyToMany(): void
    {
        $this->dashboard->bind(
            [$this->mockControlWrap, $this->mockControlWrap],
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\ManyToMany', $binding);
    }

    #[Depends('testGetBindings')]
    public function testGettingComponentsFromBinding(): void
    {
        $this->dashboard->bind($this->mockControlWrap, $this->mockChartWrap);

        /** @var \Khill\Lavacharts\Dashboards\Bindings\Binding $binding */
        $binding = $this->dashboard->getBindings()[0];

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $binding);
        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper', $binding->getControlWrappers()[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper', $binding->getChartWrappers()[0]);
    }

    #[Depends('testGetBindings')]
    #[Depends('testBindWithOneToMany')]
    public function testGetBoundChartsWithOneToMany(): void
    {
        $mockLineChartWrapper = \Mockery::mock('\Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper', [
            \Mockery::mock('\Khill\Lavacharts\Charts\LineChart')->makePartial(),
            \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['line-chart'])->makePartial()
        ])->makePartial();
        //->shouldReceive('unwrap')
        //->once()->getMock();
        //->andReturn();

        $mockAreaChartWrapper = \Mockery::mock('\Khill\Lavacharts\Dashboards\Wrappers\ChartWrapper', [
            \Mockery::mock('\Khill\Lavacharts\Charts\AreaChart')->makePartial(),
            \Mockery::mock('\Khill\Lavacharts\Values\ElementId', ['area-chart'])->makePartial()
        ])->makePartial();
        //->shouldReceive('unwrap')
        //->once()->getMock();
        //->andReturn();

        $this->dashboard->bind(
            $this->mockControlWrap,
            [$mockLineChartWrapper, $mockAreaChartWrapper]
        );

        $charts = $this->dashboard->getBoundCharts();

        $this->assertTrue(is_array($charts));
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $charts[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Charts\AreaChart', $charts[1]);
    }

    #[Depends('testGetBindings')]
    #[Depends('testBindWithOneToOne')]
    public function testSetBindingsWithMultipleOneToOne(): void
    {
        $this->dashboard->setBindings([
            [$this->mockControlWrap, $this->mockChartWrap],
            [$this->mockControlWrap, $this->mockChartWrap]
        ]);

        $bindings = $this->dashboard->getBindings();

        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $bindings[0]);
        $this->assertInstanceOf('\Khill\Lavacharts\Dashboards\Bindings\OneToOne', $bindings[1]);
    }
}
