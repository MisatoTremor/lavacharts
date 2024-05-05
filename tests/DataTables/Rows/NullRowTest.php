<?php

namespace Khill\Lavacharts\Tests\DataTables\Rows;

use Khill\Lavacharts\DataTables\Rows\Row;
use Khill\Lavacharts\Exceptions\InvalidParamType;
use Khill\Lavacharts\Tests\ProvidersTestCase;
use Khill\Lavacharts\DataTables\Rows\NullRow;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;

#[CoversMethod(NullRow::class, '__construct')]
#[CoversMethod(Row::class, 'jsonSerialize')]
class NullRowTest extends ProvidersTestCase
{
    public function testConstructorWithInt(): void
    {
        $row = new NullRow(3);

        $values = $this->inspect($row, 'values');

        array_walk($values, function ($value) {
            $this->assertNull($value->getValue());
        });
    }

    #[DataProvider('nonIntProvider')]
    public function testConstructorWithBadTypes($badTypes): void
    {
        $this->expectException(InvalidParamType::class);
        new NullRow($badTypes);
    }

    #[Depends('testConstructorWithInt')]
    public function testJsonSerialization(): void
    {
        $row = new NullRow(3);

        $json = '{"c":[{"v":null},{"v":null},{"v":null}]}';

        $this->assertEquals($json, json_encode($row));
    }
}
