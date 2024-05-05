<?php

namespace Khill\Lavacharts\Tests\Dashboards\Filters;

use \Khill\Lavacharts\Dashboards\Filters\CategoryFilter;
use Khill\Lavacharts\Exceptions\InvalidParamType;
use \Khill\Lavacharts\Tests\ProvidersTestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Depends;

#[CoversMethod(CategoryFilter::class, 'getType')]
#[CoversMethod(CategoryFilter::class, 'jsonSerialize')]
class CategoryFilterTest extends ProvidersTestCase
{
    public function testSettingColumnIndexWithConstructor(): void
    {
        $categoryFilter = new CategoryFilter(2);

        $this->assertEquals(2, $categoryFilter['filterColumnIndex']);
    }

    public function testSettingColumnLabelWithConstructor(): void
    {
        $categoryFilter = new CategoryFilter('cities');

        $this->assertEquals('cities', $categoryFilter['filterColumnLabel']);
    }

    #[Depends('testSettingColumnLabelWithConstructor')]
    public function testGetTypeMethodAndStaticReferences(): void
    {
        $categoryFilter = new CategoryFilter('cities');

        $this->assertEquals('CategoryFilter', CategoryFilter::TYPE);
        $this->assertEquals('CategoryFilter', $categoryFilter->getType());
    }

    public function testSettingColumnIndexOrLabelWithConstructorAndBadValues(): void
    {
        $this->expectException(InvalidParamType::class);
        new CategoryFilter([]);
        new CategoryFilter(1.2);
        new CategoryFilter(false);
        new CategoryFilter(new \stdClass());
    }

    #[Depends('testSettingColumnIndexWithConstructor')]
    public function testUseFormattedValue(): void
    {
        $categoryFilter = new CategoryFilter(2);

        $categoryFilter->useFormattedValue(true);
        $this->assertTrue($categoryFilter['useFormattedValue']);

        $categoryFilter->useFormattedValue(false);
        $this->assertFalse($categoryFilter['useFormattedValue']);
    }

    #[Depends('testSettingColumnLabelWithConstructor')]
    public function testJsonSerialization(): void
    {
        $categoryFilter = new CategoryFilter('age', [
            'useFormattedValue' => true,
            'ui' => [
                'caption'     => 'Ages',
                'allowTyping' => true
            ]
        ]);

        $json = '{"useFormattedValue":true,"ui":{"caption":"Ages","allowTyping":true},"filterColumnLabel":"age"}';
        $this->assertEquals($json, json_encode($categoryFilter));
    }
}
