<?php

namespace Khill\Lavacharts\Tests\Values;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Values\ElementId;
use PHPUnit\Framework\Attributes\DataProvider;

class ElementIdTest extends ProvidersTestCase
{
    public function testElementIdWithString(): void
    {
        $elementId = new ElementId('chart');

        $this->assertEquals('chart', (string) $elementId);
    }

    #[DataProvider('nonStringProvider')]
    public function testElementIdWithBadTypes($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidElementId::class);
        $elementId = new ElementId($badTypes);
    }
}
