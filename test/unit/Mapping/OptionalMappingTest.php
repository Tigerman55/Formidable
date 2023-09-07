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
use Prophecy\Argument;

#[CoversClass(OptionalMapping::class), CoversClass(MappingTrait::class)]
class OptionalMappingTest extends TestCase
{
    use MappingTraitTestTrait;

    #[Test]
    public function bindNonExistentSingleValue(): void
    {
        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->bind(Argument::any())->shouldNotBeCalled();
        $wrappedMapping->withPrefixAndRelativeKey('', 'foo')->shouldBeCalled();

        $mapping = (new OptionalMapping($wrappedMapping->reveal()))->withPrefixAndRelativeKey('', 'foo');
        $this->assertNull($mapping->bind(Data::fromFlatArray([]))->getValue());
    }

    #[Test]
    public function bindEmptySingleValue(): void
    {
        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->bind(Argument::any())->shouldNotBeCalled();
        $wrappedMapping->withPrefixAndRelativeKey('', 'foo')->shouldBeCalled();

        $mapping = (new OptionalMapping($wrappedMapping->reveal()))->withPrefixAndRelativeKey('', 'foo');
        $this->assertNull($mapping->bind(Data::fromFlatArray(['foo' => '']))->getValue());
    }

    #[Test]
    public function bindNonEmptySingleValue(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->bind($data)->willReturn(BindResult::fromValue('bar'));
        $wrappedMapping->withPrefixAndRelativeKey('', 'foo')->willReturn(clone $wrappedMapping->reveal());

        $mapping = (new OptionalMapping($wrappedMapping->reveal()))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame('bar', $mapping->bind($data)->getValue());
    }

    #[Test]
    public function bindFullyEmptyMultiValue(): void
    {
        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->bind(Argument::any())->shouldNotBeCalled();
        $wrappedMapping->withPrefixAndRelativeKey('', 'foo')->shouldBeCalled();

        $mapping = (new OptionalMapping($wrappedMapping->reveal()))->withPrefixAndRelativeKey('', 'foo');
        $this->assertNull($mapping->bind(Data::fromFlatArray(['foo[bar]' => '', 'foo[baz]' => '']))->getValue());
    }

    #[Test]
    public function bindPartiallyEmptyMultiValue(): void
    {
        $data = Data::fromFlatArray(['foo[bar]' => '', 'foo[baz]' => 'bat']);

        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->bind($data)->willReturn(BindResult::fromValue(['bar' => '', 'baz' => 'bat']));
        $wrappedMapping->withPrefixAndRelativeKey('', 'foo')->willReturn($wrappedMapping->reveal());

        $mapping = (new OptionalMapping($wrappedMapping->reveal()))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame(['bar' => '', 'baz' => 'bat'], $mapping->bind($data)->getValue());
    }

    #[Test]
    public function bindInvalidValue(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->bind($data)->willReturn(BindResult::fromFormErrors(new FormError('foo', 'bar')));
        $wrappedMapping->withPrefixAndRelativeKey('', 'foo')->willReturn($wrappedMapping->reveal());

        $mapping = (new OptionalMapping($wrappedMapping->reveal()))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame('bar', $mapping->bind($data)->getFormErrorSequence()->getIterator()->current()->getMessage());
    }

    #[Test]
    public function constraintIsAppliedToNullReturn(): void
    {
        $constraint = $this->prophesize(ConstraintInterface::class);
        $constraint->__invoke(null)->willReturn(new ValidationResult())->shouldBeCalled();

        $mapping = (new OptionalMapping($this->prophesize(MappingInterface::class)->reveal()))->verifying(
            $constraint->reveal()
        );
        $this->assertNull($mapping->bind(Data::fromFlatArray([]))->getValue());
    }

    #[Test]
    public function constraintIsAppliedToValueReturn(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $constraint = $this->prophesize(ConstraintInterface::class);
        $constraint->__invoke('bar')->willReturn(new ValidationResult(new ValidationError('bar')));

        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->bind($data)->willReturn(BindResult::fromValue('bar'));
        $wrappedMapping->withPrefixAndRelativeKey('', 'foo')->will(function () use ($wrappedMapping) {
            return $wrappedMapping->reveal();
        });

        $mapping    = (new OptionalMapping($wrappedMapping->reveal()))->verifying(
            $constraint->reveal()
        )->withPrefixAndRelativeKey('', 'foo');
        $bindResult = $mapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        self::assertSame('bar', $bindResult->getFormErrorSequence()->getIterator()->current()->getMessage());
    }

    #[Test]
    public function unbindNullValue(): void
    {
        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->unbind(Argument::any())->shouldNotBeCalled();

        $mapping = new OptionalMapping($wrappedMapping->reveal());
        self::assertTrue($mapping->unbind(null)->isEmpty());
    }

    #[Test]
    public function unbindNotNullValue(): void
    {
        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->unbind('foo')->willReturn(Data::fromFlatArray(['foo' => 'bar']));

        $mapping = new OptionalMapping($wrappedMapping->reveal());
        self::assertSame('bar', $mapping->unbind('foo')->getValue('foo'));
    }

    #[Test]
    public function createPrefixedKey(): void
    {
        $wrappedMapping = $this->prophesize(MappingInterface::class);
        $wrappedMapping->withPrefixAndRelativeKey('foo', 'bar')->shouldBeCalled();

        $mapping = (new OptionalMapping($wrappedMapping->reveal()))->withPrefixAndRelativeKey('foo', 'bar');
        $this->assertAttributeSame('foo[bar]', 'key', $mapping);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstanceForTraitTests(): MappingInterface
    {
        return new OptionalMapping($this->prophesize(MappingInterface::class)->reveal());
    }
}
