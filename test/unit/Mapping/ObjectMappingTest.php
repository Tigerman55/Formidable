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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use stdClass;
use Test\Unit\Mapping\TestAsset\SimpleObject;

use function iterator_to_array;

#[CoversClass(ObjectMapping::class), CoversClass(MappingTrait::class)]
class ObjectMappingTest extends TestCase
{
    use MappingTraitTestTrait;

    #[Test]
    public function constructionWithInvalidMappingKey(): void
    {
        $this->expectException(InvalidMappingKeyException::class);
        new ObjectMapping([1 => $this->prophesize(MappingInterface::class)->reveal()], stdClass::class);
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
        $fooMapping = $this->getMockedMapping('foo');
        $barMapping = $this->getMockedMapping('bar');

        $objectMapping = (new ObjectMapping([
            'foo' => $fooMapping,
        ], SimpleObject::class))->withMapping('bar', $barMapping);

        $this->assertAttributeSame([
            'foo' => $fooMapping,
            'bar' => $barMapping,
        ], 'mappings', $objectMapping);
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
        ], SimpleObject::class);

        $bindResult = $objectMapping->bind($data);
        self::assertTrue($bindResult->isSuccess());
        self::assertInstanceOf(SimpleObject::class, $bindResult->getValue());
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
        ], SimpleObject::class);

        $bindResult = $objectMapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        self::assertSame('bat', iterator_to_array($bindResult->getFormErrorSequence())[0]->getMessage());
    }

    #[Test]
    public function exceptionOnBind(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind($data)->willThrow(new Exception('test'));
        $mapping->withPrefixAndRelativeKey('', 'foo')->willReturn($mapping->reveal());

        $objectMapping = new ObjectMapping([
            'foo' => $mapping->reveal(),
        ], SimpleObject::class);

        $this->expectException(BindFailureException::class);
        $objectMapping->bind($data);
    }

    #[Test]
    public function bindAppliesConstraints(): void
    {
        $constraint = $this->prophesize(ConstraintInterface::class);
        $constraint->__invoke(Argument::type(SimpleObject::class))->willReturn(new ValidationResult(
            new ValidationError('error', [], 'foo[bar]')
        ));

        $data          = Data::fromFlatArray(['foo' => 'baz', 'bar' => 'bat']);
        $objectMapping = (new ObjectMapping([
            'foo' => $this->getMockedMapping('foo', 'baz', $data),
            'bar' => $this->getMockedMapping('bar', 'bat', $data),
        ], SimpleObject::class))->verifying($constraint->reveal());

        $bindResult = $objectMapping->bind($data);
        self::assertFalse($bindResult->isSuccess());
        $formError = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('error', $formError->getMessage());
        self::assertSame('foo[bar]', $formError->getKey());
    }

    #[Test]
    public function invalidApplyReturnValue(): void
    {
        $objectMapping = new ObjectMapping([], SimpleObject::class, function () {
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
        ], SimpleObject::class);

        $data = $objectMapping->unbind(new SimpleObject('baz', 'bat'));
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
        ], SimpleObject::class);

        $this->expectException(NonExistentUnapplyKeyException::class);
        $objectMapping->unbind(new SimpleObject('baz', 'bat'));
    }

    #[Test]
    public function exceptionOnUnbind(): void
    {
        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->unbind('bar')->willThrow(new Exception('test'));
        $mapping->withPrefixAndRelativeKey('', 'foo')->willReturn($mapping->reveal());

        $objectMapping = new ObjectMapping([
            'foo' => $mapping->reveal(),
        ], SimpleObject::class);

        $this->expectException(UnbindFailureException::class);
        $objectMapping->unbind(new SimpleObject('bar', ''));
    }

    #[Test]
    public function invalidUnapplyReturnValue(): void
    {
        $objectMapping = new ObjectMapping([], SimpleObject::class, null, function () {
            return null;
        });
        $this->expectException(InvalidUnapplyResultException::class);
        $objectMapping->unbind(new SimpleObject('', ''));
    }

    #[Test]
    public function createPrefixedKey(): void
    {
        $objectMapping = (new ObjectMapping([], stdClass::class))->withPrefixAndRelativeKey('foo', 'bar');
        $this->assertAttributeSame('foo[bar]', 'key', $objectMapping);
    }

    #[Test]
    public function keyCloneCreatesNewMapings(): void
    {
        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->withPrefixAndRelativeKey('foo', 'bar')->shouldBeCalled()->willReturn($mapping->reveal());
        $mapping->withPrefixAndRelativeKey('', 'bar')->shouldBeCalled()->willReturn($mapping->reveal());

        (new ObjectMapping([
            'bar' => $mapping->reveal(),
        ], stdClass::class))->withPrefixAndRelativeKey('', 'foo');
    }

    /**
     * {@inheritdoc}
     */
    protected function getInstanceForTraitTests(): MappingInterface
    {
        return new ObjectMapping([], stdClass::class);
    }

    private function getMockedMapping(
        string $key,
        ?string $value = null,
        ?Data $data = null,
        bool $success = true
    ): MappingInterface {
        $mapping = $this->prophesize(MappingInterface::class);

        if (null !== $value) {
            $mapping->unbind($value)->willReturn(Data::fromFlatArray([$key => $value]));
        }

        if (null !== $value && null !== $data) {
            $mapping->bind($data)->willReturn(
                $success
                ? BindResult::fromValue($value)
                : BindResult::fromFormErrors(new FormError($key, $value))
            );
        }

        $mapping->withPrefixAndRelativeKey('', $key)->willReturn($mapping->reveal());

        return $mapping->reveal();
    }
}
