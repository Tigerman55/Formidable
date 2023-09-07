<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use DateTimeImmutable;
use DateTimeZone;
use Formidable\Data;
use Formidable\Mapping\Formatter\DateTimeFormatter;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit_Framework_TestCase as TestCase;

use function iterator_to_array;

/**
 * @covers Formidable\Mapping\Formatter\DateTimeFormatter
 */
class DateTimeFormatterTest extends TestCase
{
    public function testBindTimeStringWithoutSeconds()
    {
        self::assertSame('01:02:00.000000', (new DateTimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '1970-01-01T01:02+00:00'])
        )->getValue()->format('H:i:s.u'));
    }

    public function testBindTimeStringWithSeconds()
    {
        self::assertSame('01:02:03.000000', (new DateTimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '1970-01-01T01:02:03+00:00'])
        )->getValue()->format('H:i:s.u'));
    }

    public function testBindTimeStringWithSecondsAndMicroseconds()
    {
        self::assertSame('01:02:03.456789', (new DateTimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '1970-01-01T01:02:03.456789+00:00'])
        )->getValue()->format('H:i:s.u'));
    }

    public function testBindToSpecificTimeZone()
    {
        $dateTime = (new DateTimeFormatter(new DateTimeZone('Europe/Berlin')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '1970-01-01T01:02:03+00:00'])
        )->getValue();

        self::assertSame('Europe/Berlin', $dateTime->getTimezone()->getName());
        self::assertSame('1970-01-01T02:02:03', $dateTime->format('Y-m-d\TH:i:s'));
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new DateTimeFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.date-time', $error->getMessage());
    }

    public function testThrowErrorOnBindNonExistentKey()
    {
        $bindResult = (new DateTimeFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    public function testUnbindDateTimeWithSeconds()
    {
        $data = (
            new DateTimeFormatter(new DateTimeZone('UTC'))
        )->unbind('foo', new DateTimeImmutable('1970-01-01 01:02:03 UTC'));
        self::assertSame('1970-01-01T01:02:03+00:00', $data->getValue('foo'));
    }

    public function testUnbindDateTimeWithSecondsAndMicroseconds()
    {
        $data = (
            new DateTimeFormatter(new DateTimeZone('UTC'))
        )->unbind('foo', new DateTimeImmutable('1970-01-01 01:02:03.456789 UTC'));
        self::assertSame('1970-01-01T01:02:03.456789+00:00', $data->getValue('foo'));
    }

    public function testUnbindDateTimeWithDifferentTimeZone()
    {
        $data = (new DateTimeFormatter(new DateTimeZone('UTC')))->unbind('foo', new DateTimeImmutable(
            '1970-01-01 01:02:03.456789',
            new DateTimeZone('Europe/Berlin')
        ));
        self::assertSame('1970-01-01T00:02:03.456789+00:00', $data->getValue('foo'));
    }

    public function testUnbindInvalidStringValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new DateTimeFormatter(new DateTimeZone('UTC')))->unbind('foo', '00:00:00');
    }
}
