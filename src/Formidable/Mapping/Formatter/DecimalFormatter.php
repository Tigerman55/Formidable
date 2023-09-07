<?php

declare(strict_types=1);

namespace Formidable\Mapping\Formatter;

use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;

use function is_numeric;
use function is_string;

final class DecimalFormatter implements FormatterInterface
{
    public function bind(string $key, Data $data): BindResult
    {
        if (! $data->hasKey($key)) {
            return BindResult::fromFormErrors(new FormError(
                $key,
                'error.required'
            ));
        }

        $value = $data->getValue($key);

        if (! is_numeric($value)) {
            return BindResult::fromFormErrors(new FormError(
                $key,
                'error.float'
            ));
        }

        return BindResult::fromValue($data->getValue($key));
    }

    public function unbind(string $key, mixed $value): Data
    {
        if (! is_string($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'string');
        } elseif (! is_numeric($value)) {
            throw InvalidTypeException::fromNonNumericString($value);
        }

        return Data::fromFlatArray([$key => $value]);
    }
}
