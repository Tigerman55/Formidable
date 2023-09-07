<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\InputText;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\InputText
 * @covers Formidable\Helper\AttributeTrait
 */
class InputTextTest extends TestCase
{
    public function testDefaultInput()
    {
        $helper = new InputText();
        self::assertSame(
            '<input type="text" id="input.foo" name="foo" value="bar&amp;">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()))
        );
    }

    public function testCustomInputType()
    {
        $helper = new InputText();
        self::assertSame(
            '<input type="email" id="input.foo" name="foo" value="bar&amp;">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['type' => 'email'])
        );
    }

    public function testCustomAttribute()
    {
        $helper = new InputText();
        self::assertSame(
            '<input data-foo="bar" type="text" id="input.foo" name="foo" value="bar&amp;">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['data-foo' => 'bar'])
        );
    }
}
