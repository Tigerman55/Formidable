<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use Exception;
use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Constraint\ConstraintInterface;
use Formidable\Mapping\Constraint\ValidationError;
use Formidable\Mapping\Constraint\ValidationResult;
use Formidable\Mapping\Exception\BindFailureException;
use Formidable\Mapping\Exception\InvalidMappingException;
use Formidable\Mapping\Exception\InvalidMappingKeyException;
use Formidable\Mapping\Exception\InvalidUnapplyResultException;
use Formidable\Mapping\Exception\MappedClassMismatchException;
use Formidable\Mapping\Exception\NonExistentMappedClassException;
use Formidable\Mapping\Exception\NonExistentUnapplyKeyException;
use Formidable\Mapping\Exception\UnbindFailureException;
use Formidable\Mapping\MappingInterface;
use Formidable\Mapping\MappingTrait;
use Formidable\Mapping\ObjectMapping;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;
use Test\Unit\Mapping\TestAsset\DTOWithObjectMapping;
use Test\Unit\Mapping\TestAsset\SimpleDTO;

use function iterator_to_array;

#[CoversClass(ObjectMapping::class), CoversClass(MappingTrait::class)]
class ObjectMappingTest extends TestCase
{
    #[Test]
    public function constructionWithInvalidMappingKey(): void
    {
        $this->expectException(InvalidMappingKeyException::class);
        new ObjectMapping([1 => self::createStub(MappingInterface::class)], stdClass::class);
    }

    #[Test]
    public function constructionWithInvalidMapping(): void
    {
        $this->expectException(InvalidMappingException::class);
        new ObjectMapping(['foo' => 'bar'], stdClass::class);
    }

    #[Test]
    public function constructionWithNonExistentClassName(): void
    {
        $this->expectException(NonExistentMappedClassException::class);
        new ObjectMapping([], 'FormidableTest\Mapping\NonExistentClassName');
    }

    #[Test]
    public function withMapping(): void
    {
        $fooFieldMapping = self::createStub(MappingInterface::class);
        $fooFieldMapping->method('bind')->willReturn(BindResult::fromValue('test1'));
        $fooFieldMapping->method('withPrefixAndRelativeKey')->willReturn($fooFieldMapping);

        $barFieldMapping = self::createStub(MappingInterface::class);
        $barFieldMapping->method('bind')->willReturn(BindResult::fromValue('test2'));
        $barFieldMapping->method('withPrefixAndRelativeKey')->willReturn($barFieldMapping);

        $objectMapping = (new ObjectMapping([
            'foo' => $fooFieldMapping,
        ], SimpleDTO::class))->withMapping('bar', $barFieldMapping);
        $simpleObject  = $objectMapping->bind(Data::fromNestedArray(['foo' => 'test1', 'bar' => 'test2']))->getValue();
        self::assertInstanceOf(SimpleDTO::class, $simpleObject);
        self::assertSame('test1', $simpleObject->foo);
        self::assertSame('test2', $simpleObject->bar);
    }

    #[Test]
    public function unbindNonMatchingClass(): void
    {
        $mapping = new ObjectMapping([], stdClass::class);
        $this->expectException(MappedClassMismatchException::class);
        $mapping->unbind('foo');
    }

    #[Test]
    public function bindValidData(): void
    {
        $data          = Data::fromFlatArray(['foo' => 'baz', 'bar' => 'bat']);
        $objectMapping = new ObjectMapping([
            'foo' => $this->getMockedMapping('foo', 'baz', $data),
            'bar' => $this->getMockedMapping('bar', 'bat', $data),
        ], SimpleDTO::class);

        $bindResult = $objectMapping->bind($data);
        self::assertTrue($bindResult->isSuccess());
        self::assertInstanceOf(SimpleDTO::class, $bindResult->getValue());
        self::assertSame('baz', $bindResult->getValue()->foo);
        self::assertSame('bat', $bindResult->getValue()->bar);
    }

    #[Test]
    public function bindInvalidData(): void
    {
        $data          = Data::fromFlatArray(['foo' => 'baz', 'bar' => 'bat']);
        $objectMapping = new ObjectMapping([
            'foo' => $this->getMockedMapping('foo', 'baz', $data),
            'bar' => $this->getMockedMapping('bar', 'bat', $data, false),
        ], SimpleDTO::class);

        $bindResult = $objectMapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        self::assertSame('bat', iterator_to_array($bindResult->getFormErrorSequence())[0]->getMessage());
    }

    #[Test]
    public function exceptionOnBind(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $mapping = $this->createMock(MappingInterface::class);
        $mapping->method('bind')->willThrowException(new Exception('test'));
        $mapping->method('withPrefixAndRelativeKey')->with('', 'foo')->willReturn($mapping);

        $objectMapping = new ObjectMapping([
            'foo' => $mapping,
        ], SimpleDTO::class);

        $this->expectException(BindFailureException::class);
        $objectMapping->bind($data);
    }

    #[Test]
    public function bindAppliesConstraints(): void
    {
        $constraint = self::createStub(ConstraintInterface::class);
        $constraint->method('__invoke')->with(self::callback(static fn (SimpleDTO $data) => true))
            ->willReturn(new ValidationResult(
                new ValidationError('error', [], 'foo[bar]')
            ));

        $data          = Data::fromFlatArray(['foo' => 'baz', 'bar' => 'bat']);
        $objectMapping = (new ObjectMapping([
            'foo' => $this->getMockedMapping('foo', 'baz', $data),
            'bar' => $this->getMockedMapping('bar', 'bat', $data),
        ], SimpleDTO::class))->verifying($constraint);

        $bindResult = $objectMapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        $formError = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('error', $formError->getMessage());
        self::assertSame('foo[bar]', $formError->getKey());
    }

    #[Test]
    public function invalidApplyReturnValue(): void
    {
        $objectMapping = new ObjectMapping([], SimpleDTO::class, function () {
            return null;
        });
        $this->expectException(MappedClassMismatchException::class);
        $objectMapping->bind(Data::none());
    }

    #[Test]
    public function unbindObject(): void
    {
        $objectMapping = new ObjectMapping([
            'foo' => $this->getMockedMapping('foo', 'baz'),
            'bar' => $this->getMockedMapping('bar', 'bat'),
        ], SimpleDTO::class);

        $data = $objectMapping->unbind(new SimpleDTO('baz', 'bat'));
        self::assertSame('baz', $data->getValue('foo'));
        self::assertSame('bat', $data->getValue('bar'));
    }

    #[Test]
    public function unbindObjectWithMissingProperty(): void
    {
        $objectMapping = new ObjectMapping([
            'foo'  => $this->getMockedMapping('foo', 'baz'),
            'bar'  => $this->getMockedMapping('bar', 'bat'),
            'none' => $this->getMockedMapping('none', 'none'),
        ], SimpleDTO::class);

        $this->expectException(NonExistentUnapplyKeyException::class);
        $objectMapping->unbind(new SimpleDTO('baz', 'bat'));
    }

    #[Test]
    public function exceptionOnUnbind(): void
    {
        $mapping = self::createStub(MappingInterface::class);
        $mapping->method('unbind')->with('bar')->willThrowException(new Exception('test'));
        $mapping->method('withPrefixAndRelativeKey')->with('', 'foo')->willReturn($mapping);

        $objectMapping = new ObjectMapping([
            'foo' => $mapping,
        ], SimpleDTO::class);

        $this->expectException(UnbindFailureException::class);
        $objectMapping->unbind(new SimpleDTO('bar', ''));
    }

    #[Test]
    public function invalidUnapplyReturnValue(): void
    {
        $objectMapping = new ObjectMapping([], SimpleDTO::class, null, function () {
            return null;
        });
        $this->expectException(InvalidUnapplyResultException::class);
        $objectMapping->unbind(new SimpleDTO('', ''));
    }

    #[Test]
    public function createPrefixedKey(): void
    {
        $fooFieldMapping = self::createStub(MappingInterface::class);
        $fooFieldMapping->method('bind')->willReturn(BindResult::fromValue('test'));
        $fooFieldMapping->method('withPrefixAndRelativeKey')->willReturn($fooFieldMapping);

        $barFieldMapping = self::createStub(MappingInterface::class);
        $barFieldMapping->method('bind')->willReturn(BindResult::fromValue('test2'));
        $barFieldMapping->method('withPrefixAndRelativeKey')->willReturn($barFieldMapping);

        $mapping = new ObjectMapping([
            'foo' => new ObjectMapping([
                'foo' => $fooFieldMapping,
                'bar' => $barFieldMapping,
            ], SimpleDTO::class),
        ], DTOWithObjectMapping::class);

        $result = $mapping->bind(Data::fromNestedArray(['foo' => ['foo' => 'test', 'bar' => 'test2']]));
        $dto    = $result->getValue();
        self::assertInstanceOf(DTOWithObjectMapping::class, $dto);
        self::assertSame($dto->foo->foo, 'test');
        self::assertSame($dto->foo->bar, 'test2');
    }

    #[Test]
    public function keyCloneCreatesNewMappings(): void
    {
        $mapping = $this->createMock(MappingInterface::class);
        $mapping->expects(self::exactly(2))->method('withPrefixAndRelativeKey')->willReturnCallback(
            static function (string $prefix, string $relativeKey) use ($mapping) {
                if (($prefix === 'foo' && $relativeKey === 'bar') || ($prefix === '' && $relativeKey === 'bar')) {
                    return $mapping;
                }

                throw new AssertionFailedError('Called too many times');
            }
        );

        (new ObjectMapping([
            'bar' => $mapping,
        ], stdClass::class))->withPrefixAndRelativeKey('', 'foo');
    }

    private function getMockedMapping(
        string $key,
        ?string $value = null,
        ?Data $data = null,
        bool $success = true
    ): MappingInterface {
        $mapping = $this->createMock(MappingInterface::class);

        if ($value !== null) {
            $mapping->method('unbind')
                ->with($value)
                ->willReturn(Data::fromFlatArray([$key => $value]));
        }

        if (null !== $value && null !== $data) {
            $mapping->method('bind')
                ->with($data)
                ->willReturn($success
                ? BindResult::fromValue($value)
                : BindResult::fromFormErrors(new FormError($key, $value)));
        }

        $mapping->method('withPrefixAndRelativeKey')->with('', $key)->willReturn($mapping);

        return $mapping;
    }
}
