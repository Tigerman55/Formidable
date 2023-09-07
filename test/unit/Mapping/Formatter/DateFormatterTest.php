<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use DateTimeImmutable;
use DateTimeZone;
use Formidable\Data;
use Formidable\Mapping\Formatter\DateFormatter;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;
use PHPUnit_Framework_TestCase as TestCase;

use function iterator_to_array;

/**
 * @covers Formidable\Mapping\Formatter\DateFormatter
 */
class DateFormatterTest extends TestCase
{
    public function testBindValidDate()
    {
        self::assertSame('2000-03-04', (new DateFormatter(new DateTimeZone('UTC')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '2000-03-04'])
        )->getValue()->format('Y-m-d'));
    }

    public function testBindToSpecificTimeZone()
    {
        self::assertSame('Europe/Berlin', (new DateFormatter(new DateTimeZone('Europe/Berlin')))->bind(
            'foo',
            Data::fromFlatArray(['foo' => '2000-03-04'])
        )->getValue()->getTimezone()->getName());
    }

    public function testBindEmptyStringValue()
    {
        $bindResult = (new DateFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray(['foo' => '']));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.date', $error->getMessage());
    }

    public function testThrowErrorOnBindNonExistentKey()
    {
        $bindResult = (new DateFormatter(new DateTimeZone('UTC')))->bind('foo', Data::fromFlatArray([]));
        self::assertFalse($bindResult->isSuccess());
        $this->assertCount(1, $bindResult->getFormErrorSequence());

        $error = iterator_to_array($bindResult->getFormErrorSequence())[0];
        self::assertSame('foo', $error->getKey());
        self::assertSame('error.required', $error->getMessage());
    }

    public function testUnbindDateTime()
    {
        $data = (new DateFormatter(new DateTimeZone('UTC')))->unbind('foo', new DateTimeImmutable('2000-03-04'));
        self::assertSame('2000-03-04', $data->getValue('foo'));
    }

    public function testUnbindDateTimeWithDifferentTimeZone()
    {
        $data = (new DateFormatter(new DateTimeZone('UTC')))->unbind('foo', new DateTimeImmutable(
            '2000-03-04 00:00:00',
            new DateTimeZone('Europe/Berlin')
        ));
        self::assertSame('2000-03-03', $data->getValue('foo'));
    }

    public function testUnbindInvalidStringValue()
    {
        $this->expectException(InvalidTypeException::class);
        (new DateFormatter(new DateTimeZone('UTC')))->unbind('foo', '2000-03-04');
    }
}
