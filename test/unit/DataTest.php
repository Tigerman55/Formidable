<?php

declare(strict_types=1);

namespace Test\Unit;

use Formidable\Data;
use Formidable\Exception\InvalidKeyException;
use Formidable\Exception\InvalidValueException;
use Formidable\Exception\NonExistentKeyException;
use Formidable\Transformer\TransformerInterface;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Data::class)]
class DataTest extends TestCase
{
    #[Test]
    public function getValueReturnsSetValue(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);
        self::assertSame('bar', $data->getValue('foo'));
    }

    #[Test]
    public function getValueReturnsFallbackWhenKeyDoesNotExist(): void
    {
        $data = Data::none();
        self::assertSame('bar', $data->getValue('foo', 'bar'));
    }

    #[Test]
    public function getValueThrowsExceptionWithoutFallback(): void
    {
        $this->expectException(NonExistentKeyException::class);
        $data = Data::none();
        $data->getValue('foo');
    }

    #[Test]
    public function hasKey(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);
        self::assertTrue($data->hasKey('foo'));
        self::assertFalse($data->hasKey('bar'));
    }

    #[Test]
    public function merge(): void
    {
        $data1      = Data::fromFlatArray(['foo' => 'bar']);
        $data2      = Data::fromFlatArray(['foo' => 'baz', 'baz' => 'bat']);
        $mergedData = $data1->merge($data2);

        self::assertSame('bar', $mergedData->getValue('foo'));
        self::assertSame('bat', $mergedData->getValue('baz'));
    }

    #[Test]
    public function filter(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar', 'baz' => 'bat'])->filter(function (string $value, string $key) {
            return $key === 'baz';
        });

        self::assertFalse($data->hasKey('foo'));
        self::assertTrue($data->hasKey('baz'));
    }

    #[Test]
    public function transform(): void
    {
        $transformer = self::createStub(TransformerInterface::class);
        $transformer->method('__invoke')->willReturnCallback(function (string $value, string $key) {
            if ($value === ' bar ' && $key === 'foo') {
                return 'bar';
            }
            if ($value === ' bat ' && $key === 'baz') {
                return ' bat';
            }

            throw new AssertionFailedError('Called with incorrect params');
        });

        $data = Data::fromFlatArray([
            'foo' => ' bar ',
            'baz' => ' bat ',
        ])->transform($transformer);

        self::assertSame('bar', $data->getValue('foo'));
        self::assertSame(' bat', $data->getValue('baz'));
    }

    #[Test]
    public function createNoneData(): void
    {
        $data = Data::none();
        self::assertSame([], $data->getData());
    }

    #[Test]
    public function createFromFlatArrayWithInvalidKey(): void
    {
        $this->expectException(InvalidKeyException::class);
        Data::fromFlatArray([0 => 'foo']);
    }

    #[Test]
    public function createFromFlatArrayWithInvalidValue(): void
    {
        $this->expectException(InvalidValueException::class);
        Data::fromFlatArray(['foo' => 0]);
    }

    #[Test]
    public function createFromNestedArray(): void
    {
        $data = Data::fromNestedArray([
            'foo' => [
                'bar' => ['baz', 'bat'],
            ],
        ]);

        self::assertSame('baz', $data->getValue('foo[bar][0]'));
        self::assertSame('bat', $data->getValue('foo[bar][1]'));
    }

    #[Test]
    public function createFromNestedArrayWithInvalidValue(): void
    {
        $this->expectException(InvalidValueException::class);
        Data::fromNestedArray(['foo' => 1]);
    }

    #[Test]
    public function createFromNestedArrayWithRootIntegerKey(): void
    {
        $this->expectException(InvalidKeyException::class);
        Data::fromNestedArray([0 => 'foo']);
    }

    #[Test]
    public function createFromNestedArrayWithChildIntegerKey(): void
    {
        $data = Data::fromNestedArray(['foo' => [0 => 'bar']]);
        self::assertSame('bar', $data->getValue('foo[0]'));
    }

    #[Test]
    public function createFromNestedArrayWithRootStringKey(): void
    {
        $data = Data::fromNestedArray(['foo' => 'bar']);
        self::assertSame('bar', $data->getValue('foo'));
    }

    #[Test]
    public function createFromNestedArrayWithChildStringKey(): void
    {
        $data = Data::fromNestedArray(['foo' => ['bar' => 'baz']]);
        self::assertSame('baz', $data->getValue('foo[bar]'));
    }

    #[Test]
    public function getIndexes(): void
    {
        $data = Data::fromNestedArray([
            'foo' => [
                'bar',
                'baz' => 'bat',
                [
                    'foo',
                    'bar',
                ],
            ],
        ]);

        self::assertSame(['0', 'baz', '1'], $data->getIndexes('foo'));
    }

    #[Test]
    public function isEmptyReturnsTrueWithoutData(): void
    {
        $data = Data::none();
        self::assertTrue($data->isEmpty());
    }

    #[Test]
    public function isEmptyReturnsFalseWithData(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);
        self::assertFalse($data->isEmpty());
    }
}
