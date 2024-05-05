<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Tests\ProvidersTestCase;
use PHPUnit\Framework\Attributes\Depends;

/**
 * @property \Khill\Lavacharts\Tests\Charts\MockChart mockChart
 */
#[\AllowDynamicProperties]
class ParentChartTest extends ProvidersTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $label = \Mockery::mock('\Khill\Lavacharts\Values\Label', ['TestChart'])->makePartial();

        $this->mockChart = new MockChart($label, $this->partialDataTable);
    }

    public function testLabelAssignedViaConstructor(): void
    {
        $this->assertEquals('TestChart', (string) $this->mockChart->getLabel());
    }

    public function testDataTable(): void
    {
        $this->mockChart->datatable($this->partialDataTable);

        $this->assertInstanceOf('\Khill\Lavacharts\DataTables\DataTable', $this->mockChart->getDataTable());
    }

    public function testCustomizeMethodToSetOptions(): void
    {
        $this->mockChart->customize([
            'title'  => 'My Cool Chart',
            'width'  => 1024,
            'height' => 768
        ]);

        $options = $this->inspect($this->mockChart, 'options');

        $this->assertArrayHasKey('title', $options);
        $this->assertEquals('My Cool Chart', $options['title']);

        $this->assertArrayHasKey('width', $options);
        $this->assertEquals(1024, $options['width']);

        $this->assertArrayHasKey('height', $options);
        $this->assertEquals(768, $options['height']);
    }

    #[Depends('testCustomizeMethodToSetOptions')]
    public function testOptionsToJson(): void
    {
        $this->mockChart->title('My Cool Chart');
        $this->mockChart->width(1024);
        $this->mockChart->height(768);

        $expected = '{"title":"My Cool Chart","width":1024,"height":768}';

        $this->assertEquals($expected, json_encode($this->mockChart));
    }
}
