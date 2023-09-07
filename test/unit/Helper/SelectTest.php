<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\AttributeTrait;
use Formidable\Helper\Exception\InvalidSelectLabelException;
use Formidable\Helper\Select;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Select::class), CoversClass(AttributeTrait::class)]
class SelectTest extends TestCase
{
    #[Test]
    public function defaultSelect(): void
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

    #[Test]
    public function integerKeys(): void
    {
        $helper = new Select();
        self::assertSame(
            "<select id=\"input.foo\" name=\"foo\"><option value=\"1\">bar</option>"
            . "<option value=\"2\">baz</option></select>",
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::none()),
                ['1' => 'bar', 2 => 'baz']
            )
        );
    }

    #[Test]
    public function singleSelectedValue(): void
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

    #[Test]
    public function multipleSelectedValues(): void
    {
        $helper = new Select();
        self::assertSame(
            "<select multiple id=\"input.foo\" name=\"foo[]\"><option value=\"foo\" selected>bar</option>"
            . "<option value=\"baz\" selected>bat</option>"
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

    #[Test]
    public function optGroups(): void
    {
        $helper = new Select();
        self::assertSame(
            "<select id=\"input.foo\" name=\"foo\"><option value=\"foo\">bar</option>"
            . "<optgroup label=\"baz\"><option value=\"bat\">a</option></optgroup></select>",
            $helper(
                new Field('foo', '', new FormErrorSequence(), Data::none()),
                ['foo' => 'bar', 'baz' => ['bat' => 'a']]
            )
        );
    }

    #[Test]
    public function exceptionOnInvalidLabel(): void
    {
        $helper = new Select();
        $this->expectException(InvalidSelectLabelException::class);
        $helper(
            new Field('foo', '', new FormErrorSequence(), Data::none()),
            ['foo' => true]
        );
    }
}
