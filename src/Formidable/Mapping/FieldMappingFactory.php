<?php

declare(strict_types=1);

namespace Formidable\Mapping;

use DateTimeZone;

final class FieldMappingFactory
{
    private static ?DateTimeZone $utcTimeZone = null;

    public static function ignored(mixed $value): FieldMapping
    {
        return new FieldMapping(new Formatter\IgnoredFormatter($value));
    }

    public static function text(int $minLength = 0, ?int $maxLength = null, string $encoding = 'utf-8'): FieldMapping
    {
        $mapping = new FieldMapping(new Formatter\TextFormatter());

        if ($minLength > 0) {
            $mapping = $mapping->verifying(new Constraint\MinLengthConstraint($minLength, $encoding));
        }

        if ($maxLength !== null) {
            $mapping = $mapping->verifying(new Constraint\MaxLengthConstraint($maxLength, $encoding));
        }

        return $mapping;
    }

    public static function nonEmptyText(
        int $minLength = 0,
        ?int $maxLength = null,
        string $encoding = 'utf-8'
    ): MappingInterface {
        return self::text($minLength, $maxLength, $encoding)->verifying(new Constraint\NotEmptyConstraint());
    }

    public static function emailAddress(): MappingInterface
    {
        return self::text()->verifying(new Constraint\EmailAddressConstraint());
    }

    public static function url(): MappingInterface
    {
        return self::text()->verifying(new Constraint\UrlConstraint());
    }

    public static function integer(?int $min = null, ?int $max = null, ?int $step = null): FieldMapping
    {
        return self::addNumberConstraints(new FieldMapping(new Formatter\IntegerFormatter()), $min, $max, $step);
    }

    public static function float(?float $min = null, ?float $max = null, ?float $step = null): FieldMapping
    {
        return self::addNumberConstraints(new FieldMapping(new Formatter\FloatFormatter()), $min, $max, $step);
    }

    public static function decimal(?string $min = null, ?string $max = null, ?string $step = null): FieldMapping
    {
        return self::addNumberConstraints(new FieldMapping(new Formatter\DecimalFormatter()), $min, $max, $step);
    }

    public static function boolean(): FieldMapping
    {
        return new FieldMapping(new Formatter\BooleanFormatter());
    }

    public static function time(?DateTimeZone $timeZone = null): FieldMapping
    {
        return new FieldMapping(new Formatter\TimeFormatter($timeZone ?: self::getUtcTimeZone()));
    }

    public static function date(?DateTimeZone $timeZone = null): FieldMapping
    {
        return new FieldMapping(new Formatter\DateFormatter($timeZone ?: self::getUtcTimeZone()));
    }

    public static function dateTime(?DateTimeZone $timeZone = null, bool $localTime = false): FieldMapping
    {
        return new FieldMapping(new Formatter\DateTimeFormatter($timeZone ?: self::getUtcTimeZone(), $localTime));
    }

    private static function addNumberConstraints(
        FieldMapping $mapping,
        int|float|string|null $min,
        int|float|string|null $max,
        int|float|string|null $step
    ): FieldMapping {
        if ($min !== null) {
            $mapping = $mapping->verifying(new Constraint\MinNumberConstraint($min));
        }

        if ($max !== null) {
            $mapping = $mapping->verifying(new Constraint\MaxNumberConstraint($max));
        }

        if ($step !== null) {
            $mapping = $mapping->verifying(new Constraint\StepNumberConstraint($step, $min));
        }

        return $mapping;
    }

    private static function getUtcTimeZone(): DateTimeZone
    {
        if (self::$utcTimeZone === null) {
            self::$utcTimeZone = new DateTimeZone('UTC');
        }

        return self::$utcTimeZone;
    }
}
