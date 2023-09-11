<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Constraint\ConstraintInterface;
use Formidable\Mapping\Constraint\ValidationError;
use Formidable\Mapping\Constraint\ValidationResult;
use Formidable\Mapping\Exception\InvalidTypeException;
use Formidable\Mapping\MappingInterface;
use Formidable\Mapping\MappingTrait;
use Formidable\Mapping\RepeatedMapping;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function in_array;

#[CoversClass(RepeatedMapping::class), CoversClass(MappingTrait::class)]
class RepeatedMappingTest extends TestCase
{
    #[Test]
    public function bindValidArray(): void
    {
        $data = Data::fromNestedArray([
            'foo' => [
                'bar' => [
                    'baz',
                ],
            ],
        ]);

        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('withPrefixAndRelativeKey')->with('foo[bar]', '0')->willReturn($wrappedMapping);
        $wrappedMapping->method('bind')->with($data)->willReturn(BindResult::fromValue('baz'));

        $mapping    = (new RepeatedMapping($wrappedMapping))->withPrefixAndRelativeKey('foo', 'bar');
        $bindResult = $mapping->bind($data);
        self::assertTrue($bindResult->isSuccess());
        self::assertSame(['baz'], $bindResult->getValue());
    }

    #[Test]
    public function bindPartiallyValidArray(): void
    {
        $data = Data::fromNestedArray([
            'foo' => [
                'bar' => [
                    'baz',
                    'bat',
                ],
            ],
        ]);

        $bazMapping = self::createStub(MappingInterface::class);
        $bazMapping->method('bind')->with($data)->willReturn(BindResult::fromValue('baz'));
        $batMapping = self::createStub(MappingInterface::class);
        $batMapping->method('bind')->with($data)->willReturn(BindResult::fromFormErrors(new FormError('', 'bar')));

        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('withPrefixAndRelativeKey')
            ->willReturnCallback(function (string $prefix, string $relativeKey) use ($bazMapping, $batMapping) {
                if ($prefix !== 'foo[bar]') {
                    throw new AssertionFailedError('invalid prefix given');
                }

                return match ($relativeKey) {
                    '0' => $bazMapping,
                    '1' => $batMapping,
                    default => throw new AssertionFailedError('invalid relative key given')
                };
            });

        $mapping    = (new RepeatedMapping($wrappedMapping))->withPrefixAndRelativeKey('foo', 'bar');
        $bindResult = $mapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        self::assertSame('bar', $bindResult->getFormErrorSequence()->getIterator()->current()->getMessage());
    }

    #[Test]
    public function bindAppliesConstraintsToValidResult(): void
    {
        $data = Data::fromNestedArray([
            'foo' => [
                'bar' => [
                    'baz',
                ],
            ],
        ]);

        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('withPrefixAndRelativeKey')->with('foo[bar]', '0')->willReturn($wrappedMapping);
        $wrappedMapping->method('bind')->with($data)->willReturn(BindResult::fromValue('baz'));

        $constraint = self::createStub(ConstraintInterface::class);
        $constraint->method('__invoke')->with(['baz'])
            ->willReturn(new ValidationResult(new ValidationError('bar', [], '0')));

        $mapping    = (new RepeatedMapping($wrappedMapping))
            ->withPrefixAndRelativeKey('foo', 'bar')->verifying(
                $constraint
            );
        $bindResult = $mapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        self::assertSame('bar', $bindResult->getFormErrorSequence()->getIterator()->current()->getMessage());
        self::assertSame('foo[bar][0]', $bindResult->getFormErrorSequence()->getIterator()->current()->getKey());
    }

    #[Test]
    public function unbindInvalidValue(): void
    {
        $mapping = new RepeatedMapping(self::createStub(MappingInterface::class));
        $this->expectException(InvalidTypeException::class);
        $mapping->unbind('test');
    }

    #[Test]
    public function unbindValidValues(): void
    {
        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('unbind')->willReturnCallback(static fn (string $value) => match ($value) {
            'baz' => Data::fromFlatArray(['foo[bar][0]' => 'baz']),
            'bat' => Data::fromFlatArray(['foo[bar][1]' => 'bat']),
            default => throw new AssertionFailedError('Invalid value given')
        });
        $wrappedMapping->method('withPrefixAndRelativeKey')
            ->willReturnCallback(function (string $prefix, string $relativeKey) use ($wrappedMapping) {
                if ($prefix !== 'foo[bar]' || ! in_array($relativeKey, ['0', '1'], true)) {
                    throw new AssertionFailedError('invalid prefix or relative key given');
                }

                return $wrappedMapping;
            });

        $mapping = (new RepeatedMapping($wrappedMapping))->withPrefixAndRelativeKey('foo', 'bar');
        $data    = $mapping->unbind(['baz', 'bat']);
        self::assertSame('baz', $data->getValue('foo[bar][0]'));
        self::assertSame('bat', $data->getValue('foo[bar][1]'));
    }

    #[Test]
    public function createPrefixedKey(): void
    {
        $wrappedMapping = self::createStub(MappingInterface::class);
        $wrappedMapping->method('bind')->willReturn(BindResult::fromValue('test'));
        $wrappedMapping->method('withPrefixAndRelativeKey')->willReturn($wrappedMapping);

        $mapping = (new RepeatedMapping($wrappedMapping))->withPrefixAndRelativeKey('foo', 'bar');
        $result  = $mapping->bind(Data::fromNestedArray(['foo' => ['bar' => ['test']]]));
        self::assertSame('test', $result->getValue()[0]);
    }

    protected function getInstanceForTraitTests(): MappingInterface
    {
        return new RepeatedMapping(self::createStub(MappingInterface::class));
    }
}
