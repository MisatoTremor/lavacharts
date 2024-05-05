<?php

namespace Khill\Lavacharts\Tests\Support;

use PHPUnit\Framework\TestCase;

class ArrayReduceTest extends TestCase
{
    public $mixedTypes;
    public $randomTypes;
    public $sameTypes;

    public function setUp(): void
    {
        parent::setUp();

        $this->sameTypes = [
            new Foo, new Foo, new Foo
        ];

        $this->mixedTypes = [
            new Foo, new Bar, new Foo
        ];

        $this->randomTypes = [
            new Foo, [], new Foo, 5
        ];
    }

    public function testArrayReduceWithSameType(): void
    {
        $check = array_reduce($this->sameTypes, function ($prev, $curr) {
            return $prev && $curr instanceof Foo;
        }, true);

        $this->assertTrue($check);
    }

    public function testArrayReduceWithMixedTypes(): void
    {
        $check = array_reduce($this->mixedTypes, function ($prev, $curr) {
            return $prev && $curr instanceof Foo;
        }, true);

        $this->assertFalse($check);
    }

    public function testArrayReduceWithRandomTypes(): void
    {
        $check = array_reduce($this->randomTypes, function ($prev, $curr) {
            return $prev && $curr instanceof Foo;
        }, true);

        $this->assertFalse($check);
    }
}
