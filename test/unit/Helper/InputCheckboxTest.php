<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\AttributeTrait;
use Formidable\Helper\InputCheckbox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InputCheckbox::class), CoversClass(AttributeTrait::class)]
class InputCheckboxTest extends TestCase
{
    #[Test]
    public function defaultInputWithEmptyValue(): void
    {
        $helper = new InputCheckbox();
        self::assertSame(
            '<input type="checkbox" id="input.foo" name="foo" value="true">',
            $helper(new Field('foo', '', new FormErrorSequence(), Data::none()))
        );
    }

    #[Test]
    public function defaultInputWithTrueValue(): void
    {
        $helper = new InputCheckbox();
        self::assertSame(
            '<input type="checkbox" id="input.foo" name="foo" value="true" checked>',
            $helper(new Field('foo', 'true', new FormErrorSequence(), Data::none()))
        );
    }

    #[Test]
    public function customAttribute(): void
    {
        $helper = new InputCheckbox();
        self::assertSame(
            '<input data-foo="bar" type="checkbox" id="input.foo" name="foo" value="true">',
            $helper(new Field('foo', '', new FormErrorSequence(), Data::none()), ['data-foo' => 'bar'])
        );
    }
}
