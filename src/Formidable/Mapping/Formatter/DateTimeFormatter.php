<?php

declare(strict_types=1);

namespace Formidable\Mapping\Formatter;

use DateTimeImmutable;
use DateTimeZone;
use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;

use function preg_match;
use function sprintf;

final class DateTimeFormatter implements FormatterInterface
{
    public function __construct(private readonly DateTimeZone $timeZone, private readonly bool $localTime = false)
    {
    }

    public function bind(string $key, Data $data): BindResult
    {
        if (! $data->hasKey($key)) {
            return BindResult::fromFormErrors(new FormError(
                $key,
                'error.required'
            ));
        }

        // Technically, seconds must always be present, according to the spec, but at least Chrome seems to omit them.
        if (
            ! preg_match(
                '(^
                (?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})[Tt]
                (?<hour>\d{2}):(?<minute>\d{2})(?::(?<second>\d{2})(?:\.(?<microsecond>\d{1,6}))?)?
                (?<timezone>[Zz]|[+-]\d{2}:\d{2})?
            $)x',
                $data->getValue($key),
                $matches
            )
        ) {
            return BindResult::fromFormErrors(new FormError(
                $key,
                'error.date-time'
            ));
        }

        /** @var DateTimeImmutable $dateTime */
        $dateTime = DateTimeImmutable::createFromFormat(
            '!Y-m-d\TH:i:s.u' . ($this->localTime ? '' : 'P'),
            sprintf(
                '%s-%s-%sT%s:%s:%s.%s%s',
                $matches['year'],
                $matches['month'],
                $matches['day'],
                $matches['hour'],
                $matches['minute'],
                ! empty($matches['second']) ? $matches['second'] : '00',
                ! empty($matches['microsecond']) ? $matches['microsecond'] : '00',
                $matches['timezone'] ?? ''
            ),
            $this->timeZone
        );
        return BindResult::fromValue($dateTime->setTimezone($this->timeZone));
    }

    public function unbind(string $key, mixed $value): Data
    {
        if (! $value instanceof DateTimeImmutable) {
            throw InvalidTypeException::fromInvalidType($value, 'DateTimeImmutable');
        }

        $dateTime     = $value->setTimezone($this->timeZone);
        $timeZoneFlag = $this->localTime ? '' : 'P';

        if ((int) $dateTime->format('u') > 0) {
            return Data::fromFlatArray([$key => $dateTime->format('Y-m-d\TH:i:s.u' . $timeZoneFlag)]);
        }

        return Data::fromFlatArray([$key => $dateTime->format('Y-m-d\TH:i:s' . $timeZoneFlag)]);
    }
}
