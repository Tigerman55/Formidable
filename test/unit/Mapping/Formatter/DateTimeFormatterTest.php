<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use DateTimeImmutable;
use DateTimeZone;
use Formidable\Data;
use Formidable\Mapping\Formatter\DateTimeFormatter;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(DateTimeFormatter::class)]
class DateTimeFormatterTest extends TestCase
{
    #[Test]
    public function bindTimeStringWithoutSeconds(): void
    {
        self::assertSame('01:02:00.000000', (new DateTimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '1970-01-01T01:02+00:00'])
        )->getValue()->format('H:i:s.u'));
    }

    #[Test]
    public function bindTimeStringWithSeconds(): void
    {
        self::assertSame('01:02:03.000000', (new DateTimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '1970-01-01T01:02:03+00:00'])
        )->getValue()->format('H:i:s.u'));
    }

    #[Test]
    public function bindTimeStringWithSecondsAndMicroseconds(): void
    {
        self::assertSame('01:02:03.456789', (new DateTimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '1970-01-01T01:02:03.456789+00:00'])
        )->getValue()->format('H:i:s.u'));
    }

    #[Test]
    public function bindToSpecificTimeZone(): void
    {
        $dateTime = (new DateTimeFormatter(new DateTimeZone('Europe/Berlin')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '1970-01-01T01:02:03+00:00'])
        )->getValue();

        self::assertSame('Europe/Berlin', $dateTime->getTimezone()->getName());
        self::assertSame('1970-01-01T02:02:03', $dateTime->format('Y-m-d\TH:i:s'));
    }

    #[Test]
    public function bindEmptyStringValue(): void
    {
        $bindResult = (new DateTimeFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.date-time', $error->getMessage());
    }

    #[Test]
    public function throwErrorOnBindNonExistentKey(): void
    {
        $bindResult = (new DateTimeFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    #[Test]
    public function unbindDateTimeWithSeconds(): void
    {
        $data = (
            new DateTimeFormatter(new DateTimeZone('UTC'))
        )->unbind('foo', new DateTimeImmutable('1970-01-01 01:02:03 UTC'));
        self::assertSame('1970-01-01T01:02:03+00:00', $data->getValue('foo'));
    }

    #[Test]
    public function unbindDateTimeWithSecondsAndMicroseconds(): void
    {
        $data = (
            new DateTimeFormatter(new DateTimeZone('UTC'))
        )->unbind('foo', new DateTimeImmutable('1970-01-01 01:02:03.456789 UTC'));
        self::assertSame('1970-01-01T01:02:03.456789+00:00', $data->getValue('foo'));
    }

    #[Test]
    public function unbindDateTimeWithDifferentTimeZone(): void
    {
        $data = (new DateTimeFormatter(new DateTimeZone('UTC')))->unbind('foo', new DateTimeImmutable(
            '1970-01-01 01:02:03.456789',
            new DateTimeZone('Europe/Berlin')
        ));
        self::assertSame('1970-01-01T00:02:03.456789+00:00', $data->getValue('foo'));
    }

    #[Test]
    public function unbindInvalidStringValue(): void
    {
        $this->expectException(InvalidTypeException::class);
        (new DateTimeFormatter(new DateTimeZone('UTC')))->unbind('foo', '00:00:00');
    }
}
