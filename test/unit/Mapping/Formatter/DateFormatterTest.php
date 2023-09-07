<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use DateTimeImmutable;
use DateTimeZone;
use Formidable\Data;
use Formidable\Mapping\Formatter\DateFormatter;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(DateFormatter::class)]
class DateFormatterTest extends TestCase
{
    #[Test]
    public function bindValidDate(): void
    {
        self::assertSame('2000-03-04', (new DateFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '2000-03-04'])
        )->getValue()->format('Y-m-d'));
    }

    #[Test]
    public function bindToSpecificTimeZone(): void
    {
        self::assertSame('Europe/Berlin', (new DateFormatter(new DateTimeZone('Europe/Berlin')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '2000-03-04'])
        )->getValue()->getTimezone()->getName());
    }

    #[Test]
    public function bindEmptyStringValue(): void
    {
        $bindResult = (new DateFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.date', $error->getMessage());
    }

    #[Test]
    public function throwErrorOnBindNonExistentKey(): void
    {
        $bindResult = (new DateFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    #[Test]
    public function unbindDateTime(): void
    {
        $data = (new DateFormatter(new DateTimeZone('UTC')))->unbind('foo', new DateTimeImmutable('2000-03-04'));
        self::assertSame('2000-03-04', $data->getValue('foo'));
    }

    #[Test]
    public function unbindDateTimeWithDifferentTimeZone(): void
    {
        $data = (new DateFormatter(new DateTimeZone('UTC')))->unbind('foo', new DateTimeImmutable(
            '2000-03-04 00:00:00',
            new DateTimeZone('Europe/Berlin')
        ));
        self::assertSame('2000-03-03', $data->getValue('foo'));
    }

    #[Test]
    public function unbindInvalidStringValue(): void
    {
        $this->expectException(InvalidTypeException::class);
        (new DateFormatter(new DateTimeZone('UTC')))->unbind('foo', '2000-03-04');
    }
}
