<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use DateTimeZone;
use Formidable\Mapping\Constraint\EmailAddressConstraint;
use Formidable\Mapping\Constraint\NotEmptyConstraint;
use Formidable\Mapping\Constraint\UrlConstraint;
use Formidable\Mapping\FieldMapping;
use Formidable\Mapping\FieldMappingFactory;
use Formidable\Mapping\Formatter\BooleanFormatter;
use Formidable\Mapping\Formatter\DateFormatter;
use Formidable\Mapping\Formatter\DateTimeFormatter;
use Formidable\Mapping\Formatter\DecimalFormatter;
use Formidable\Mapping\Formatter\FloatFormatter;
use Formidable\Mapping\Formatter\IgnoredFormatter;
use Formidable\Mapping\Formatter\IntegerFormatter;
use Formidable\Mapping\Formatter\TextFormatter;
use Formidable\Mapping\Formatter\TimeFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldMappingFactory::class)]
class FieldMappingFactoryTest extends TestCase
{
    #[Test]
    public function ignoredFactory(): void
    {
        $fieldMapping = FieldMappingFactory::ignored('foo');
        self::assertAttributeInstanceOf(IgnoredFormatter::class, 'binder', $fieldMapping);
        self::assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    #[Test]
    public function textFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::text();
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    #[Test]
    public function textFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::text(1, 2, 'iso-8859-15');
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(2, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeSame('iso-8859-15', 'encoding', $constraints[0]);
        $this->assertAttributeSame(1, 'lengthLimit', $constraints[0]);

        $this->assertAttributeSame('iso-8859-15', 'encoding', $constraints[1]);
        $this->assertAttributeSame(2, 'lengthLimit', $constraints[1]);
    }

    #[Test]
    public function nonEmptyTextFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::nonEmptyText();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(1, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        self::assertInstanceOf(NotEmptyConstraint::class, $constraints[0]);
    }

    #[Test]
    public function nonEmptyTextFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::nonEmptyText(1, 2, 'iso-8859-15');
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(3, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeSame('iso-8859-15', 'encoding', $constraints[0]);
        $this->assertAttributeSame(1, 'lengthLimit', $constraints[0]);

        $this->assertAttributeSame('iso-8859-15', 'encoding', $constraints[1]);
        $this->assertAttributeSame(2, 'lengthLimit', $constraints[1]);

        self::assertInstanceOf(NotEmptyConstraint::class, $constraints[2]);
    }

    #[Test]
    public function emailAddressFactory(): void
    {
        $fieldMapping = FieldMappingFactory::emailAddress();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(1, 'constraints', $fieldMapping);
        self::assertInstanceOf(EmailAddressConstraint::class, self::readAttribute($fieldMapping, 'constraints')[0]);
    }

    #[Test]
    public function urlFactory(): void
    {
        $fieldMapping = FieldMappingFactory::url();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TextFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(1, 'constraints', $fieldMapping);
        self::assertInstanceOf(UrlConstraint::class, self::readAttribute($fieldMapping, 'constraints')[0]);
    }

    #[Test]
    public function integerFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::integer();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(IntegerFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    #[Test]
    public function integerFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::integer(1, 3, 2);
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(IntegerFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(3, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeEquals('1', 'limit', $constraints[0]);
        $this->assertAttributeEquals('3', 'limit', $constraints[1]);
        $this->assertAttributeEquals('2', 'step', $constraints[2]);
        $this->assertAttributeEquals('1', 'base', $constraints[2]);
    }

    #[Test]
    public function floatFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::float();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(FloatFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    #[Test]
    public function floatFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::float(1.5, 3., 0.5);
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(FloatFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(3, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeEquals('1.5', 'limit', $constraints[0]);
        $this->assertAttributeEquals('3', 'limit', $constraints[1]);
        $this->assertAttributeEquals('0.5', 'step', $constraints[2]);
        $this->assertAttributeEquals('1.5', 'base', $constraints[2]);
    }

    #[Test]
    public function decimalFactoryWithoutConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::decimal();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(DecimalFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    #[Test]
    public function decimalFactoryWithConstraints(): void
    {
        $fieldMapping = FieldMappingFactory::decimal('1.5', '3', '0.5');
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(DecimalFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(3, 'constraints', $fieldMapping);

        $constraints = self::readAttribute($fieldMapping, 'constraints');

        $this->assertAttributeEquals('1.5', 'limit', $constraints[0]);
        $this->assertAttributeEquals('3', 'limit', $constraints[1]);
        $this->assertAttributeEquals('0.5', 'step', $constraints[2]);
        $this->assertAttributeEquals('1.5', 'base', $constraints[2]);
    }

    #[Test]
    public function booleanFactory(): void
    {
        $fieldMapping = FieldMappingFactory::boolean();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(BooleanFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);
    }

    #[Test]
    public function defaultTimeFactory(): void
    {
        $fieldMapping = FieldMappingFactory::time();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(TimeFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);

        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone  = self::readAttribute($formatter, 'timeZone');
        self::assertSame('UTC', $timeZone->getName());
    }

    #[Test]
    public function timeFactoryWithOptions(): void
    {
        $fieldMapping = FieldMappingFactory::time(new DateTimeZone('Europe/Berlin'));
        $formatter    = self::readAttribute($fieldMapping, 'binder');
        $timeZone     = self::readAttribute($formatter, 'timeZone');
        self::assertSame('Europe/Berlin', $timeZone->getName());
    }

    #[Test]
    public function dateFactory(): void
    {
        $fieldMapping = FieldMappingFactory::date();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(DateFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);

        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone  = self::readAttribute($formatter, 'timeZone');
        self::assertSame('UTC', $timeZone->getName());
    }

    #[Test]
    public function dateFactoryWithOptions(): void
    {
        $fieldMapping = FieldMappingFactory::date(new DateTimeZone('Europe/Berlin'));
        $formatter    = self::readAttribute($fieldMapping, 'binder');
        $timeZone     = self::readAttribute($formatter, 'timeZone');
        self::assertSame('Europe/Berlin', $timeZone->getName());
    }

    #[Test]
    public function dateTimeFactory(): void
    {
        $fieldMapping = FieldMappingFactory::dateTime();
        self::assertInstanceOf(FieldMapping::class, $fieldMapping);
        $this->assertAttributeInstanceOf(DateTimeFormatter::class, 'binder', $fieldMapping);
        $this->assertAttributeCount(0, 'constraints', $fieldMapping);

        $formatter = self::readAttribute($fieldMapping, 'binder');
        $timeZone  = self::readAttribute($formatter, 'timeZone');
        self::assertSame('UTC', $timeZone->getName());
        $this->assertAttributeSame(false, 'localTime', $formatter);
    }

    #[Test]
    public function dateTimeFactoryWithOptions(): void
    {
        $fieldMapping = FieldMappingFactory::dateTime(new DateTimeZone('Europe/Berlin'), true);
        $formatter    = self::readAttribute($fieldMapping, 'binder');
        $timeZone     = self::readAttribute($formatter, 'timeZone');
        self::assertSame('Europe/Berlin', $timeZone->getName());
        $this->assertAttributeSame(true, 'localTime', $formatter);
    }
}
