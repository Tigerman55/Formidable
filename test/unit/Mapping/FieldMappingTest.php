<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use Formidable\Data;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Constraint\ConstraintInterface;
use Formidable\Mapping\Constraint\ValidationError;
use Formidable\Mapping\Constraint\ValidationResult;
use Formidable\Mapping\FieldMapping;
use Formidable\Mapping\Formatter\FormatterInterface;
use Formidable\Mapping\Formatter\TextFormatter;
use Formidable\Mapping\MappingTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldMapping::class), CoversClass(MappingTrait::class)]
class FieldMappingTest extends TestCase
{
    #[Test]
    public function bindReturnsFailureResult(): void
    {
        $data       = Data::fromFlatArray(['foo' => 'bar']);
        $bindResult = BindResult::fromFormErrors();

        $binder = self::createStub(FormatterInterface::class);
        $binder->method('bind')->with('foo', $data)->willReturn($bindResult);

        $mapping = (new FieldMapping($binder))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame($bindResult, $mapping->bind($data));
    }

    #[Test]
    public function bindReturnsSuccessResult(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $binder = self::createStub(FormatterInterface::class);
        $binder->method('bind')->with('foo', $data)->willReturn(BindResult::fromValue('bar'));

        $mapping    = (new FieldMapping($binder))->withPrefixAndRelativeKey('', 'foo');
        $bindResult = $mapping->bind($data);
        self::assertTrue($bindResult->isSuccess());
        self::assertSame('bar', $bindResult->getValue());
    }

    #[Test]
    public function bindAppliesConstraints(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $binder = self::createStub(FormatterInterface::class);
        $binder->method('bind')->with('foo', $data)->willReturn(BindResult::fromValue('bar'));

        $constraint = self::createStub(ConstraintInterface::class);
        $constraint->method('__invoke')->with('bar')->willReturn(new ValidationResult(new ValidationError('bar')));

        $mapping    = (new FieldMapping($binder))->withPrefixAndRelativeKey('', 'foo')->verifying(
            $constraint
        );
        $bindResult = $mapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        self::assertSame('bar', $bindResult->getFormErrorSequence()->getIterator()->current()->message);
        self::assertSame('foo', $bindResult->getFormErrorSequence()->getIterator()->current()->key);
    }

    #[Test]
    public function unbind(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $binder = self::createStub(FormatterInterface::class);
        $binder->method('unbind')->with('foo', 'bar')->willReturn($data);

        $mapping = (new FieldMapping($binder))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame($data, $mapping->unbind('bar'));
    }

    #[Test]
    public function createPrefixedKey(): void
    {
        $mapping = (new FieldMapping(new TextFormatter()))->withPrefixAndRelativeKey('', 'foo');
        $result  = $mapping->bind(Data::fromNestedArray(['foo' => 'test']));
        self::assertSame('test', $result->getValue());
    }
}
