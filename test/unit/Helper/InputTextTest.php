<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\AttributeTrait;
use Formidable\Helper\InputText;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InputText::class), CoversClass(AttributeTrait::class)]
class InputTextTest extends TestCase
{
    #[Test]
    public function defaultInput(): void
    {
        $helper = new InputText();
        self::assertSame(
            '<input type="text" id="input.foo" name="foo" value="bar&amp;">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()))
        );
    }

    #[Test]
    public function customInputType(): void
    {
        $helper = new InputText();
        self::assertSame(
            '<input type="email" id="input.foo" name="foo" value="bar&amp;">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['type' => 'email'])
        );
    }

    #[Test]
    public function customAttribute(): void
    {
        $helper = new InputText();
        self::assertSame(
            '<input data-foo="bar" type="text" id="input.foo" name="foo" value="bar&amp;">',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['data-foo' => 'bar'])
        );
    }
}
