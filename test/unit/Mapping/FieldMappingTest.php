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
use Formidable\Mapping\MappingInterface;
use Formidable\Mapping\MappingTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldMapping::class), CoversClass(MappingTrait::class)]
class FieldMappingTest extends TestCase
{
    use MappingTraitTestTrait;

    #[Test]
    public function bindReturnsFailureResult(): void
    {
        $data       = Data::fromFlatArray(['foo' => 'bar']);
        $bindResult = BindResult::fromFormErrors();

        $binder = $this->prophesize(FormatterInterface::class);
        $binder->bind('foo', $data)->willReturn($bindResult);

        $mapping = (new FieldMapping($binder->reveal()))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame($bindResult, $mapping->bind($data));
    }

    #[Test]
    public function bindReturnsSuccessResult(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $binder = $this->prophesize(FormatterInterface::class);
        $binder->bind('foo', $data)->willReturn(BindResult::fromValue('bar'));

        $mapping    = (new FieldMapping($binder->reveal()))->withPrefixAndRelativeKey('', 'foo');
        $bindResult = $mapping->bind($data);
        self::assertTrue($bindResult->isSuccess());
        self::assertSame('bar', $bindResult->getValue());
    }

    #[Test]
    public function bindAppliesConstraints(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $binder = $this->prophesize(FormatterInterface::class);
        $binder->bind('foo', $data)->willReturn(BindResult::fromValue('bar'));

        $constraint = $this->prophesize(ConstraintInterface::class);
        $constraint->__invoke('bar')->willReturn(new ValidationResult(new ValidationError('bar')));

        $mapping    = (new FieldMapping($binder->reveal()))->withPrefixAndRelativeKey('', 'foo')->verifying(
            $constraint->reveal()
        );
        $bindResult = $mapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        self::assertSame('bar', $bindResult->getFormErrorSequence()->getIterator()->current()->getMessage());
        self::assertSame('foo', $bindResult->getFormErrorSequence()->getIterator()->current()->getKey());
    }

    #[Test]
    public function unbind(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $binder = $this->prophesize(FormatterInterface::class);
        $binder->unbind('foo', 'bar')->willReturn($data);

        $mapping = (new FieldMapping($binder->reveal()))->withPrefixAndRelativeKey('', 'foo');
        self::assertSame($data, $mapping->unbind('bar'));
    }

    #[Test]
    public function createPrefixedKey(): void
    {
        $binder = $this->prophesize(FormatterInterface::class);

        $mapping = (new FieldMapping($binder->reveal()))->withPrefixAndRelativeKey('foo', 'bar');
        $this->assertAttributeSame('foo[bar]', 'key', $mapping);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstanceForTraitTests(): MappingInterface
    {
        return new FieldMapping($this->prophesize(FormatterInterface::class)->reveal());
    }
}
