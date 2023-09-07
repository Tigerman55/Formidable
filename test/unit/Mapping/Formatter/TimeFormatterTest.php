<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use DateTimeImmutable;
use DateTimeZone;
use Formidable\Data;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Formidable\Mapping\Formatter\TimeFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

#[CoversClass(TimeFormatter::class)]
class TimeFormatterTest extends TestCase
{
    #[Test]
    public function bindTimeStringWithoutSeconds(): void
    {
        self::assertSame('01:02:00.000000', (new TimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '01:02'])
        )->getValue()->format('H:i:s.u'));
    }

    #[Test]
    public function bindTimeStringWithSeconds(): void
    {
        self::assertSame('01:02:03.000000', (new TimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '01:02:03'])
        )->getValue()->format('H:i:s.u'));
    }

    #[Test]
    public function bindTimeStringWithSecondsAndMicroseconds(): void
    {
        self::assertSame('01:02:03.456789', (new TimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '01:02:03.456789'])
        )->getValue()->format('H:i:s.u'));
    }

    #[Test]
    public function bindToSpecificTimeZone(): void
    {
        self::assertSame('Europe/Berlin', (new TimeFormatter(new DateTimeZone('Europe/Berlin')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '01:02:03'])
        )->getValue()->getTimezone()->getName());
    }

    #[Test]
    public function bindEmptyStringValue(): void
    {
        $bindResult = (new TimeFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        self::assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.time', $error->getMessage());
    }

    #[Test]
    public function throwErrorOnBindNonExistentKey(): void
    {
        $bindResult = (new TimeFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray([]));
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
            new TimeFormatter(new DateTimeZone('UTC'))
        )->unbind('foo', new DateTimeImmutable('1970-01-01 01:02:03 UTC'));
        self::assertSame('01:02:03', $data->getValue('foo'));
    }

    #[Test]
    public function unbindDateTimeWithSecondsAndMicroseconds(): void
    {
        $data = (
            new TimeFormatter(new DateTimeZone('UTC'))
        )->unbind('foo', new DateTimeImmutable('1970-01-01 01:02:03.456789 UTC'));
        self::assertSame('01:02:03.456789', $data->getValue('foo'));
    }

    #[Test]
    public function unbindDateTimeWithDifferentTimeZone(): void
    {
        $data = (new TimeFormatter(new DateTimeZone('UTC')))->unbind('foo', new DateTimeImmutable(
            '1970-01-01 01:02:03.456789',
            new DateTimeZone('Europe/Berlin')
        ));
        self::assertSame('00:02:03.456789', $data->getValue('foo'));
    }

    #[Test]
    public function unbindInvalidStringValue(): void
    {
        $this->expectException(InvalidTypeException::class);
        (new TimeFormatter(new DateTimeZone('UTC')))->unbind('foo', '00:00:00');
    }
}
