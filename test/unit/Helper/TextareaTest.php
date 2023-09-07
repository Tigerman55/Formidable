<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\Textarea;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\Textarea
 * @covers Formidable\Helper\AttributeTrait
 */
class TextareaTest extends TestCase
{
    public function testDefaultTextarea()
    {
        $helper = new Textarea();
        self::assertSame(
            '<textarea id="input.foo" name="foo">bar&amp;</textarea>',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()))
        );
    }

    public function testEmptyTextarea()
    {
        $helper = new Textarea();
        self::assertSame(
            '<textarea id="input.foo" name="foo"></textarea>',
            $helper(new Field('foo', '', new FormErrorSequence(), Data::none()))
        );
    }

    public function testCustomAttribute()
    {
        $helper = new Textarea();
        self::assertSame(
            '<textarea data-foo="bar" id="input.foo" name="foo">bar&amp;</textarea>',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['data-foo' => 'bar'])
        );
    }
}
