<?php

namespace Khill\Lavacharts\Tests\Dashboards;

use Khill\Lavacharts\Dashboards\Wrappers\Wrapper;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Depends;

#[CoversMethod(Wrapper::class, 'getJsClass')]
class ControlWrapperTest extends ProvidersTestCase
{
    public $mockElementId;
    public $jsonOutput;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockElementId = $this->getMockElementId('TestLabel');

        $this->jsonOutput = '{"options":{"Option1":5,"Option2":true},"containerId":"TestLabel","controlType":"NumberRangeFilter"}';
    }

    public function getMockFilter()
    {
        return \Mockery::mock('\Khill\Lavacharts\Dashboards\Filters\NumberRangeFilter')
            ->shouldReceive('getType')
            ->once()
            ->andReturn('NumberRangeFilter')
            ->shouldReceive('getWrapType')
            ->once()
            ->andReturn('controlType')
            ->shouldReceive('jsonSerialize')
            ->once()
            ->andReturn([
                'Option1' => 5,
                'Option2' => true
            ])
            ->getMock();
    }

    public function testGetJsClass(): void
    {
        $filter = \Mockery::mock('\Khill\Lavacharts\Dashboards\Filters\StringFilter');

        $controlWrapper = new ControlWrapper($filter, $this->mockElementId);

        $javascript = 'google.visualization.ControlWrapper';

        $this->assertEquals($javascript, $controlWrapper->getJsClass());
    }

    public function testJsonSerialize(): void
    {
        $filter = $this->getMockFilter();

        $controlWrapper = new ControlWrapper($filter, $this->mockElementId);

        $this->assertEquals($this->jsonOutput, json_encode($controlWrapper));
    }

    #[Depends('testJsonSerialize')]
    public function testToJson(): void
    {
        $filter = $this->getMockFilter();

        $controlWrapper = new ControlWrapper($filter, $this->mockElementId);

        $this->assertEquals($this->jsonOutput, $controlWrapper->toJson());
    }

    #[Depends('testGetJsClass')]
    #[Depends('testToJson')]
    public function testGetJsConstructor(): void
    {
        $filter = $this->getMockFilter();

        $controlWrapper = new ControlWrapper($filter, $this->mockElementId);

        $this->assertEquals(
            'new google.visualization.ControlWrapper('.$this->jsonOutput.')',
            $controlWrapper->getJsConstructor()
        );
    }
}
