<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\Exception\InvalidSelectLabelException;
use Formidable\Helper\Select;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\Select
 * @covers Formidable\Helper\AttributeTrait
 */
class SelectTest extends TestCase
{
    public function testDefaultSelect()
    {
        $helper = new Select();
        self::assertSame(
            '<select id="input.foo" name="foo"><option value="foo">bar</option></select>',
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::none()),
                ['foo' => 'bar']
            )
        );
    }

    public function testIntegerKeys()
    {
        $helper = new Select();
        self::assertSame(
            "<select id=\"input.foo\" name=\"foo\"><option value=\"1\">bar</option>\n"
            . "<option value=\"2\">baz</option></select>",
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::none()),
                ['1' => 'bar', 2 => 'baz']
            )
        );
    }

    public function testSingleSelectedValue()
    {
        $helper = new Select();
        self::assertSame(
            '<select id="input.foo" name="foo"><option value="foo" selected>bar</option></select>',
            $helper(
                new Field('foo', 'foo', new FormErrorSequence(), Data::none()),
                ['foo' => 'bar']
            )
        );
    }

    public function testMultipleSelectedValues()
    {
        $helper = new Select();
        self::assertSame(
            "<select multiple id=\"input.foo\" name=\"foo[]\"><option value=\"foo\" selected>bar</option>\n"
            . "<option value=\"baz\" selected>bat</option>\n"
            . "<option value=\"a\">b</option></select>",
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::fromNestedArray([
                    'foo' => [
                        'foo',
                        'baz',
                    ],
                ])),
                ['foo' => 'bar', 'baz' => 'bat', 'a' => 'b'],
                ['multiple' => 'multiple']
            )
        );
    }

    public function testOptGroups()
    {
        $helper = new Select();
        self::assertSame(
            "<select id=\"input.foo\" name=\"foo\"><option value=\"foo\">bar</option>\n"
            . "<optgroup label=\"baz\"><option value=\"bat\">a</option></optgroup></select>",
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::none()),
                ['foo' => 'bar', 'baz' => ['bat' => 'a']]
            )
        );
    }

    public function testExceptionOnInvalidLabel()
    {
        $helper = new Select();
        $this->expectException(InvalidSelectLabelException::class);
        $helper(
            new Field('foo', '', new FormErrorSequence(), Data::none()),
            ['foo' => true]
        );
    }
}
