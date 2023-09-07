<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\InputPassword;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\InputPassword
 * @covers Formidable\Helper\AttributeTrait
 */
class InputPasswordTest extends TestCase
{
    public function testDefaultInput()
    {
        $helper = new InputPassword();
        self::assertSame(
            '<input type="password" id="input.foo" name="foo">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()))
        );
    }

    public function testInputTypeCannotBeOverriden()
    {
        $helper = new InputPassword();
        self::assertSame(
            '<input type="password" id="input.foo" name="foo">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['type' => 'text'])
        );
    }

    public function testCustomAttribute()
    {
        $helper = new InputPassword();
        self::assertSame(
            '<input data-foo="bar" type="password" id="input.foo" name="foo">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['data-foo' => 'bar'])
        );
    }
}
