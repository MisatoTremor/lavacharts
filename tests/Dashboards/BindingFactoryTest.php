<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Bindings\BindingFactory;
use Khill\Lavacharts\Dashboards\Bindings\ManyToMany;
use Khill\Lavacharts\Dashboards\Bindings\ManyToOne;
use Khill\Lavacharts\Dashboards\Bindings\OneToMany;
use Khill\Lavacharts\Dashboards\Bindings\OneToOne;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(OneToOne::class)]
#[CoversClass(OneToMany::class)]
#[CoversClass(ManyToOne::class)]
#[CoversClass(ManyToMany::class)]
#[CoversMethod(BindingFactory::class, 'create')]
class BindingFactoryTest extends DashboardsTestCase
{
    private BindingFactory $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new BindingFactory;
    }

    public function testBindWithOneToOne(): void
    {
        $binding = $this->factory->create($this->mockControlWrap, $this->mockChartWrap);

        $this->assertInstanceOf(OneToOne::class, $binding);
    }

    public function testBindWithOneToMany(): void
    {
        $binding = $this->factory->create(
            $this->mockControlWrap,
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        $this->assertInstanceOf(OneToMany::class, $binding);
    }

    public function testBindWithManyToOne(): void
    {
        $binding = $this->factory->create(
            [$this->mockControlWrap, $this->mockControlWrap],
            $this->mockChartWrap
        );

        $this->assertInstanceOf(ManyToOne::class, $binding);
    }

    public function testBindWithManyToMany(): void
    {
        $binding = $this->factory->create(
            [$this->mockControlWrap, $this->mockControlWrap],
            [$this->mockChartWrap, $this->mockChartWrap]
        );

        $this->assertInstanceOf(ManyToMany::class, $binding);
    }
}
