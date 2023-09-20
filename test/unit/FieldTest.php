<?php

declare(strict_types=1);

namespace Test\Unit;

use Formidable\Data;
use Formidable\Field;
use Formidable\FormError\FormError;
use Formidable\FormError\FormErrorSequence;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Field::class)]
class FieldTest extends TestCase
{
    #[Test]
    public function keyRetrieval(): void
    {
        $field = new Field('foo', '', new FormErrorSequence(), Data::none());
        self::assertSame('foo', $field->key);
    }

    #[Test]
    public function valueRetrieval(): void
    {
        $field = new Field('', 'foo', new FormErrorSequence(), Data::none());
        self::assertSame('foo', $field->value);
    }

    #[Test]
    public function errorRetrieval(): void
    {
        $errors = new FormErrorSequence();
        $field  = new Field('', '', $errors, Data::none());
        self::assertSame($errors, $field->errors);
    }

    #[Test]
    public function hasErrorsReturnsFalseWithoutErrors(): void
    {
        $errors = new FormErrorSequence();
        $field  = new Field('', '', $errors, Data::none());
        self::assertFalse($field->hasErrors());
    }

    #[Test]
    public function hasErrorsReturnsTrueWithErrors(): void
    {
        $errors = new FormErrorSequence(new FormError('', ''));
        $field  = new Field('', '', $errors, Data::none());
        self::assertTrue($field->hasErrors());
    }

    #[Test]
    public function getIndexes(): void
    {
        $field = new Field('foo', '', new FormErrorSequence(), Data::fromFlatArray([
            'foo[0]'      => 'bar0',
            'foo[1]'      => 'bar1',
            'foo[2][baz]' => 'bar2',
        ]));

        self::assertSame(['0', '1', '2'], $field->getIndexes());
    }

    #[Test]
    public function getNestedValues(): void
    {
        $field = new Field('foo', '', new FormErrorSequence(), Data::fromFlatArray([
            'foo[0]'      => 'bar0',
            'foo[1]'      => 'bar1',
            'foo[1][baz]' => 'bar2',
        ]));

        self::assertSame(['bar0', 'bar1'], $field->getNestedValues());
    }

    #[Test]
    public function getNestedValuesPreserveKeys(): void
    {
        $field = new Field('foo', '', new FormErrorSequence(), Data::fromFlatArray([
            'foo[bar]'    => 'bar0',
            'foo[baz]'    => 'baz0',
            'foo[1][baz]' => 'bar2',
        ]));

        self::assertSame([
            'bar' => 'bar0',
            'baz' => 'baz0',
        ], $field->getNestedValues(true));
    }

    #[Test]
    public function getNestedValuesNoPreserveKeys(): void
    {
        $field = new Field('foo', '', new FormErrorSequence(), Data::fromFlatArray([
            'foo[bar]'    => 'bar0',
            'foo[baz]'    => 'baz0',
            'foo[1][baz]' => 'bar2',
        ]));

        self::assertSame(['bar0', 'baz0'], $field->getNestedValues());
    }
}
