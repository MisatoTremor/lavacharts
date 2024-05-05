<?php

namespace Khill\Lavacharts\Tests\Builders;

use Khill\Lavacharts\Builders\ChartBuilder;
use Khill\Lavacharts\Charts\LineChart;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use PHPUnit\Framework\Attributes\Depends;

class ChartBuilderTest extends ProvidersTestCase
{
    private ChartBuilder $builder;

    public function setUp(): void
    {
        parent::setUp();

        $this->builder = new ChartBuilder();
    }

    public function testWithLabelAndDataTable(): void
    {
        $this->builder->setType('LineChart');
        $this->builder->setLabel('taco');
        $this->builder->setDatatable($this->getMockDataTable());

        $chart = $this->builder->getChart();

        $this->assertInstanceOf('\Khill\Lavacharts\Charts\LineChart', $chart);
        $this->assertEquals('taco', $chart->getLabelStr());
        $this->assertInstanceOf('\Khill\Lavacharts\Datatables\Datatable', $chart->getDataTable());
    }

    #[Depends('testWithLabelAndDataTable')]
    public function testWithLabelAndDataTableAndOptions(): void
    {
        $this->builder->setType('LineChart');
        $this->builder->setLabel('taco');
        $this->builder->setDatatable($this->getMockDataTable());
        $this->builder->setOptions(['tacos' => 'good']);

        $chart = $this->builder->getChart();
        $options = $chart->getOptions();

        $this->assertArrayHasKey('tacos', $options);
        $this->assertEquals('good', $options['tacos']);
    }

    #[Depends('testWithLabelAndDataTable')]
    #[Depends('testWithLabelAndDataTableAndOptions')]
    public function testWithLabelAndDataTableAndOptionsAndElementId(): void
    {
        $this->builder->setType('LineChart');
        $this->builder->setLabel('taco');
        $this->builder->setDatatable($this->getMockDataTable());
        $this->builder->setOptions(['tacos' => 'good']);
        $this->builder->setElementId('platter');

        $chart = $this->builder->getChart();

        $elementId = $this->inspect($chart, 'elementId');

        $this->assertInstanceOf('\Khill\Lavacharts\Values\ElementId', $elementId);
        $this->assertEquals('platter', (string) $elementId);
    }
}
