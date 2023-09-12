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

final class DateFormatter implements FormatterInterface
{
    /** @var DateTimeZone */
    private $timeZone;

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

        $dateTime = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $data->getValue($key),
            $this->timeZone
        );

        if ($dateTime === false) {
            return BindResult::fromFormErrors(new FormError(
                $key,
                'error.date'
            ));
        }

        return BindResult::fromValue($dateTime);
    }

    public function unbind(string $key, mixed $value): Data
    {
        if (! $value instanceof DateTimeInterface) {
            throw InvalidTypeException::fromInvalidType($value, 'DateTimeInterface');
        }

        $dateTime = $value->setTimezone($this->timeZone);

        return Data::fromFlatArray([$key => $dateTime->format('Y-m-d')]);
    }
}
