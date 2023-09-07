<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\AttributeTrait;
use Formidable\Helper\Textarea;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Textarea::class), CoversClass(AttributeTrait::class)]
class TextareaTest extends TestCase
{
    #[Test]
    public function defaultTextarea(): void
    {
        $helper = new Textarea();
        self::assertSame(
            '<textarea id="input.foo" name="foo">bar&amp;</textarea>',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()))
        );
    }

    #[Test]
    public function emptyTextarea(): void
    {
        $helper = new Textarea();
        self::assertSame(
            '<textarea id="input.foo" name="foo"></textarea>',
            $helper(new Field('foo', '', new FormErrorSequence(), Data::none()))
        );
    }

    #[Test]
    public function customAttribute(): void
    {
        $helper = new Textarea();
        self::assertSame(
            '<textarea data-foo="bar" id="input.foo" name="foo">bar&amp;</textarea>',
            $helper(new Field('foo', 'bar&', new FormErrorSequence(), Data::none()), ['data-foo' => 'bar'])
        );
    }
}
