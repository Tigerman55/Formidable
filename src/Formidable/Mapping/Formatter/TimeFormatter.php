<?php

declare(strict_types=1);

namespace Formidable\Mapping\Formatter;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;

use function preg_match;
use function sprintf;

final class TimeFormatter implements FormatterInterface
{
    private DateTimeZone $timeZone;

    public function __construct(DateTimeZone $timeZone)
    {
        $this->timeZone = $timeZone;
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
                '(^(?<hour>\d{2}):(?<minute>\d{2})(?::(?<second>\d{2})(?:\.(?<microsecond>\d{1,6}))?)?$)',
                $data->getValue($key),
                $matches
            )
        ) {
            return BindResult::fromFormErrors(new FormError(
                $key,
                'error.time'
            ));
        }

        return BindResult::fromValue(DateTimeImmutable::createFromFormat(
            '!H:i:s.u',
            sprintf(
                '%s:%s:%s.%s',
                $matches['hour'],
                $matches['minute'],
                $matches['second'] ?? '00',
                $matches['microsecond'] ?? '0'
            ),
            $this->timeZone
        ));
    }

    public function unbind(string $key, mixed $value): Data
    {
        if (! $value instanceof DateTimeInterface) {
            throw InvalidTypeException::fromInvalidType($value, 'DateTimeInterface');
        }

        $dateTime = $value->setTimezone($this->timeZone);

        if ((int) $dateTime->format('u') > 0) {
            return Data::fromFlatArray([$key => $dateTime->format('H:i:s.u')]);
        }

        return Data::fromFlatArray([$key => $dateTime->format('H:i:s')]);
    }
}
