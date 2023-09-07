<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\AttributeTrait;
use Formidable\Helper\InputPassword;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InputPassword::class), CoversClass(AttributeTrait::class)]
class InputPasswordTest extends TestCase
{
    #[Test]
    public function defaultInput(): void
    {
        $helper = new InputPassword();
        self::assertSame(
            '<input type="password" id="input.foo" name="foo">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()))
        );
    }

    #[Test]
    public function inputTypeCannotBeOverridden(): void
    {
        $helper = new InputPassword();
        self::assertSame(
            '<input type="password" id="input.foo" name="foo">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['type' => 'text'])
        );
    }

    #[Test]
    public function customAttribute(): void
    {
        $helper = new InputPassword();
        self::assertSame(
            '<input data-foo="bar" type="password" id="input.foo" name="foo">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['data-foo' => 'bar'])
        );
    }
}
