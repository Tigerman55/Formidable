<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use DateTimeImmutable;
use DateTimeZone;
use Formidable\Data;
use Formidable\Mapping\FieldMapping;
use Formidable\Mapping\FieldMappingFactory;
use Formidable\Mapping\MappingTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldMappingFactory::class), CoversClass(FieldMapping::class), CoversClass(MappingTrait::class)]
class FieldMappingFactoryTest extends TestCase
{
    #[Test]
    public function ignoredFactory(): void
    {
        $fieldMapping = FieldMappingFactory::ignored('foo');
        $result       = $fieldMapping->bind(Data::fromFlatArray(['foo' => 'testSkippedData']));
        self::assertSame('foo', $result->getValue());
    }

    #[Test]
    public function textFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::text();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'bar']));
        self::assertSame('bar', $result->getValue());
    }

    #[Test]
    public function textFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::text(1, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'a']));
        self::assertSame('a', $result->getValue());

        $fieldMapping = FieldMappingFactory::text(1, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'ab']));
        self::assertSame('ab', $result->getValue());

        $fieldMapping = FieldMappingFactory::text(1, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'abc']));
        self::assertCount(1, $result->getFormErrorSequence());
    }

    #[Test]
    public function nonEmptyTextFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::nonEmptyText();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'ab']));
        self::assertSame('ab', $result->getValue());

        // non-empty text does not allow empty string
        $fieldMapping = FieldMappingFactory::nonEmptyText();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '']));
        self::assertCount(1, $result->getFormErrorSequence());
    }

    #[Test]
    public function nonEmptyTextFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::nonEmptyText(1, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'a']));
        self::assertSame('a', $result->getValue());

        $fieldMapping = FieldMappingFactory::nonEmptyText(1, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'ab']));
        self::assertSame('ab', $result->getValue());

        $fieldMapping = FieldMappingFactory::nonEmptyText(1, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'abc']));
        self::assertCount(1, $result->getFormErrorSequence());

        $fieldMapping = FieldMappingFactory::nonEmptyText(1, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '']));
        self::assertCount(2, $result->getFormErrorSequence());
    }

    #[Test]
    public function emailAddressFactory(): void
    {
        $fieldMapping = FieldMappingFactory::emailAddress();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'test@domain.com']));
        self::assertSame('test@domain.com', $result->getValue());
    }

    #[Test]
    public function urlFactory(): void
    {
        $fieldMapping = FieldMappingFactory::url();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'https://www.example.com']));
        self::assertSame('https://www.example.com', $result->getValue());
    }

    #[Test]
    public function integerFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::integer();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '1']));
        self::assertSame(1, $result->getValue());
    }

    #[Test]
    public function integerFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::integer(1, 3, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '1']));
        self::assertSame(1, $result->getValue());

        $fieldMapping = FieldMappingFactory::integer(1, 3, 2);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '0']));
        self::assertCount(2, $result->getFormErrorSequence());
    }

    #[Test]
    public function floatFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::float();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '0.5']));
        self::assertSame(0.5, $result->getValue());
    }

    #[Test]
    public function floatFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::float(1.5, 3.0, 0.5);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '1.5']));
        self::assertSame(1.5, $result->getValue());

        $fieldMapping = FieldMappingFactory::float(1.5, 3.0, 0.5);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '3']));
        self::assertSame(3.0, $result->getValue());

        $fieldMapping = FieldMappingFactory::float(1.5, 3.0, 0.5);
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '3.5']));
        self::assertCount(1, $result->getFormErrorSequence());
    }

    #[Test]
    public function decimalFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::decimal();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '0.5']));
        self::assertSame('0.5', $result->getValue());
    }

    #[Test]
    public function decimalFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::decimal('1.5', '3.0', '0.5');
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '1.5']));
        self::assertSame('1.5', $result->getValue());

        $fieldMapping = FieldMappingFactory::decimal('1.5', '3.0', '0.5');
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '3']));
        self::assertSame('3', $result->getValue());

        $fieldMapping = FieldMappingFactory::decimal('1.5', '3.0', '0.5');
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => '3.5']));
        self::assertCount(1, $result->getFormErrorSequence());
    }

    #[Test]
    public function booleanFactory(): void
    {
        $fieldMapping = FieldMappingFactory::boolean();
        $result       = $fieldMapping->bind(Data::fromFlatArray(['' => 'false']));
        self::assertFalse($result->getValue());
    }

    #[Test]
    public function defaultTimeFactory(): void
    {
        $fieldMapping = FieldMappingFactory::time();
        $time         = $fieldMapping->bind(Data::fromFlatArray(['' => '13:30']))->getValue();
        self::assertInstanceOf(DateTimeImmutable::class, $time);
        self::assertSame('UTC', $time->getTimezone()->getName());
        self::assertSame('13:30', $time->format('H:i'));
    }

    #[Test]
    public function timeFactoryWithOptions(): void
    {
        $fieldMapping = FieldMappingFactory::time(new DateTimeZone('America/New_York'));
        $time         = $fieldMapping->bind(Data::fromFlatArray(['' => '13:30']))->getValue();
        self::assertInstanceOf(DateTimeImmutable::class, $time);
        self::assertSame('America/New_York', $time->getTimezone()->getName());
        self::assertSame('13:30', $time->format('H:i'));
    }

    #[Test]
    public function dateFactory(): void
    {
        $fieldMapping = FieldMappingFactory::date();
        $date         = $fieldMapping->bind(Data::fromFlatArray(['' => '2023-09-01']))->getValue();
        self::assertInstanceOf(DateTimeImmutable::class, $date);
        self::assertSame($date->getTimezone()->getName(), 'UTC');
        self::assertSame('2023-09-01', $date->format('Y-m-d'));
    }

    #[Test]
    public function dateFactoryWithOptions(): void
    {
        $fieldMapping = FieldMappingFactory::date(new DateTimeZone('America/New_York'));
        $date         = $fieldMapping->bind(Data::fromFlatArray(['' => '2023-09-01']))->getValue();
        self::assertInstanceOf(DateTimeImmutable::class, $date);
        self::assertSame('America/New_York', $date->getTimezone()->getName());
        self::assertSame('2023-09-01', $date->format('Y-m-d'));
    }

    #[Test]
    public function dateTimeFactory(): void
    {
        $fieldMapping = FieldMappingFactory::dateTime();
        $dateTime     = $fieldMapping->bind(Data::fromFlatArray(['' => '2023-09-01T13:30+00:00']))->getValue();
        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame('2023-09-01 13:30:00', $dateTime->format('Y-m-d H:i:s'));
        self::assertSame('UTC', $dateTime->getTimezone()->getName());
    }

    #[Test]
    public function dateTimeFactoryWithOptions(): void
    {
        $fieldMapping = FieldMappingFactory::dateTime(new DateTimeZone('America/New_York'), true);
        $dateTime     = $fieldMapping->bind(Data::fromFlatArray(['' => '2023-09-01T13:30']))->getValue();
        self::assertInstanceOf(DateTimeImmutable::class, $dateTime);
        self::assertSame('2023-09-01 13:30:00', $dateTime->format('Y-m-d H:i:s'));
        self::assertSame('America/New_York', $dateTime->getTimezone()->getName());
    }
}
