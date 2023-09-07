<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\InputCheckbox;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\InputCheckbox
 * @covers Formidable\Helper\AttributeTrait
 */
class InputCheckboxTest extends TestCase
{
    public function testDefaultInputWithEmptyValue()
    {
        $helper = new InputCheckbox();
        self::assertSame(
            '<input type="checkbox" id="input.foo" name="foo" value="true">',
            $helper(new Field('foo', '', new FormErrorSequence(), Data::none()))
        );
    }

    public function testDefaultInputWithTrueValue()
    {
        $helper = new InputCheckbox();
        self::assertSame(
            '<input type="checkbox" id="input.foo" name="foo" value="true" checked>',
            $helper(new Field('foo', 'true', new FormErrorSequence(), Data::none()))
        );
    }

    public function testCustomAttribute()
    {
        $helper = new InputCheckbox();
        self::assertSame(
            '<input data-foo="bar" type="checkbox" id="input.foo" name="foo" value="true">',
            $helper(new Field('foo', '', new FormErrorSequence(), Data::none()), ['data-foo' => 'bar'])
        );
    }
}
