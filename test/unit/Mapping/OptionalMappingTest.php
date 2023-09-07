<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Constraint\ConstraintInterface;
use Formidable\Mapping\Constraint\ValidationError;
use Formidable\Mapping\Constraint\ValidationResult;
use Formidable\Mapping\MappingInterface;
use Formidable\Mapping\MappingTrait;
use Formidable\Mapping\OptionalMapping;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(OptionalMapping::class), CoversClass(MappingTrait::class)]
class OptionalMappingTest extends TestCase
{
    use MappingTraitTestTrait;

    #[Test]
    public function bindNonExistentSingleValue(): void
    {
        $wrappedMapping = $this->createMock(MappingInterface::class);
        $wrappedMapping->expects(self::never())->method('bind');
        $wrappedMapping->expects(self::once())->method('withPrefixAndRelativeKey')->with('', 'foo');

        $mapping = (new OptionalMapping($wrappedMapping))->withPrefixAndRelativeKey('', 'foo');
        $this->assertNull($mapping->bind(Data::fromFlatArray([]))->getValue());
    }

    #[Test]
    public function bindEmptySingleValue(): void
    {
        $wrappedMapping = $this->createMock(MappingInterface::class);
        $wrappedMapping->expects(self::never())->method('bind');
        $wrappedMapping->expects(self::once())->method('withPrefixAndRelativeKey')->with('', 'foo');

        $mapping = (new OptionalMapping($wrappedMapping))->withPrefixAndRelativeKey('', 'foo');
        $this->assertNull($mapping->bind(Data::fromFlatArray(['foo' => '']))->getValue());
    }

    #[Test]
    public function bindNonEmptySingleValue(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('bind')->with($data)->willReturn(BindResult::fromValue('bar'));
        $wrappedMapping->method('withPrefixAndRelativeKey')->with('', 'foo')->willReturn($wrappedMapping);

        $mapping = (new OptionalMapping($wrappedMapping))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame('bar', $mapping->bind($data)->getValue());
    }

    #[Test]
    public function bindFullyEmptyMultiValue(): void
    {
        $wrappedMapping = $this->createMock(MappingInterface::class);
        $wrappedMapping->expects(self::never())->method('bind');
        $wrappedMapping->expects(self::once())->method('withPrefixAndRelativeKey')->with('', 'foo');

        $mapping = (new OptionalMapping($wrappedMapping))->withPrefixAndRelativeKey('', 'foo');
        $this->assertNull($mapping->bind(Data::fromFlatArray(['foo[bar]' => '', 'foo[baz]' => '']))->getValue());
    }

    #[Test]
    public function bindPartiallyEmptyMultiValue(): void
    {
        $data = Data::fromFlatArray(['foo[bar]' => '', 'foo[baz]' => 'bat']);

        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('bind')->with($data)->willReturn(BindResult::fromValue(['bar' => '', 'baz' => 'bat']));
        $wrappedMapping->method('withPrefixAndRelativeKey')->with('', 'foo')->willReturn($wrappedMapping);

        $mapping = (new OptionalMapping($wrappedMapping))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame(['bar' => '', 'baz' => 'bat'], $mapping->bind($data)->getValue());
    }

    #[Test]
    public function bindInvalidValue(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('bind')->with($data)->willReturn(BindResult::fromFormErrors(new FormError('foo', 'bar')));
        $wrappedMapping->method('withPrefixAndRelativeKey')->with('', 'foo')->willReturn($wrappedMapping);

        $mapping = (new OptionalMapping($wrappedMapping))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame('bar', $mapping->bind($data)->getFormErrorSequence()->getIterator()->current()->getMessage());
    }

    #[Test]
    public function constraintIsAppliedToNullReturn(): void
    {
        $constraint = $this->createMock(ConstraintInterface::class);
        $constraint->expects(self::once())->method('__invoke')->with(null)->willReturn(new ValidationResult());

        $mapping = (new OptionalMapping(self::createStub(MappingInterface::class)))->verifying(
            $constraint
        );
        $this->assertNull($mapping->bind(Data::fromFlatArray([]))->getValue());
    }

    #[Test]
    public function constraintIsAppliedToValueReturn(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $constraint = self::createStub(ConstraintInterface::class);
        $constraint->method('__invoke')->with('bar')->willReturn(new ValidationResult(new ValidationError('bar')));

        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('bind')->with($data)->willReturn(BindResult::fromValue('bar'));
        $wrappedMapping->method('withPrefixAndRelativeKey')->with('', 'foo')->willReturn($wrappedMapping);

        $mapping    = (new OptionalMapping($wrappedMapping))->verifying($constraint)->withPrefixAndRelativeKey('', 'foo');
        $bindResult = $mapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        self::assertSame('bar', $bindResult->getFormErrorSequence()->getIterator()->current()->getMessage());
    }

    #[Test]
    public function unbindNullValue(): void
    {
        $wrappedMapping = $this->createMock(MappingInterface::class);
        $wrappedMapping->expects(self::never())->method('unbind');

        $mapping = new OptionalMapping($wrappedMapping);
        self::assertTrue($mapping->unbind(null)->isEmpty());
    }

    #[Test]
    public function unbindNotNullValue(): void
    {
        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('unbind')->with('foo')->willReturn(Data::fromFlatArray(['foo' => 'bar']));

        $mapping = new OptionalMapping($wrappedMapping);
        self::assertSame('bar', $mapping->unbind('foo')->getValue('foo'));
    }

    #[Test]
    public function createPrefixedKey(): void
    {
        $wrappedMapping = $this->createMock(MappingInterface::class);
        $wrappedMapping->expects(self::once())->method('withPrefixAndRelativeKey')->with('foo', 'bar');

        $mapping = (new OptionalMapping($wrappedMapping))->withPrefixAndRelativeKey('foo', 'bar');
        $this->assertAttributeSame('foo[bar]', 'key', $mapping);
    }

    protected function getInstanceForTraitTests(): MappingInterface
    {
        return new OptionalMapping(self::createStub(MappingInterface::class));
    }
}
