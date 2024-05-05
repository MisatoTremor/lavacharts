<?php

namespace Khill\Lavacharts\Tests\Values;

use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\Values\Label;
use PHPUnit\Framework\Attributes\DataProvider;

class LabelTest extends ProvidersTestCase
{
    public function testLabelWithString(): void
    {
        $label = new Label('TheChart');

        $this->assertEquals('TheChart', (string) $label);
    }

    #[DataProvider('nonStringProvider')]
    public function testLabelWithBadTypes($badTypes): void
    {
        $this->expectException(\Khill\Lavacharts\Exceptions\InvalidLabel::class);
        $label = new Label($badTypes);
    }
}
