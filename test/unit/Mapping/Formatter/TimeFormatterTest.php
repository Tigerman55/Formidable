<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use DateTimeImmutable;
use DateTimeZone;
use Formidable\Data;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use Formidable\Mapping\Formatter\TimeFormatter;
use PHPUnit_Framework_TestCase as TestCase;

use function iterator_to_array;

/**
 * @covers Formidable\Mapping\Formatter\TimeFormatter
 */
class TimeFormatterTest extends TestCase
{
    public function testBindTimeStringWithoutSeconds()
    {
        self::assertSame('01:02:00.000000', (new TimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '01:02'])
        )->getValue()->format('H:i:s.u'));
    }

    public function testBindTimeStringWithSeconds()
    {
        self::assertSame('01:02:03.000000', (new TimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '01:02:03'])
        )->getValue()->format('H:i:s.u'));
    }

    public function testBindTimeStringWithSecondsAndMicroseconds()
    {
        self::assertSame('01:02:03.456789', (new TimeFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '01:02:03.456789'])
        )->getValue()->format('H:i:s.u'));
    }

    public function testBindToSpecificTimeZone()
    {
        self::assertSame('Europe/Berlin', (new TimeFormatter(new DateTimeZone('Europe/Berlin')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '01:02:03'])
        )->getValue()->getTimezone()->getName());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new TimeFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.time', $error->getMessage());
    }

    public function testThrowErrorOnBindNonExistentKey()
    {
        $bindResult = (new TimeFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    public function testUnbindDateTimeWithSeconds()
    {
        $data = (
            new TimeFormatter(new DateTimeZone('UTC'))
        )->unbind('foo', new DateTimeImmutable('1970-01-01 01:02:03 UTC'));
        self::assertSame('01:02:03', $data->getValue('foo'));
    }

    public function testUnbindDateTimeWithSecondsAndMicroseconds()
    {
        $data = (
            new TimeFormatter(new DateTimeZone('UTC'))
        )->unbind('foo', new DateTimeImmutable('1970-01-01 01:02:03.456789 UTC'));
        self::assertSame('01:02:03.456789', $data->getValue('foo'));
    }

    public function testUnbindDateTimeWithDifferentTimeZone()
    {
        $data = (new TimeFormatter(new DateTimeZone('UTC')))->unbind('foo', new DateTimeImmutable(
            '1970-01-01 01:02:03.456789',
            new DateTimeZone('Europe/Berlin')
        ));
        self::assertSame('00:02:03.456789', $data->getValue('foo'));
    }

    public function testUnbindInvalidStringValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new TimeFormatter(new DateTimeZone('UTC')))->unbind('foo', '00:00:00');
    }
}
