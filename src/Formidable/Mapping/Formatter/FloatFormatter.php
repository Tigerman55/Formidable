<?php

declare(strict_types=1);

namespace Formidable\Mapping\Formatter;

use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;

use function is_float;
use function is_numeric;

final class FloatFormatter implements FormatterInterface
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

        return BindResult::fromValue((float) $data->getValue($key));
    }

    public function unbind(string $key, mixed $value): Data
    {
        if (! is_float($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'float');
        }

        return Data::fromFlatArray([$key => (string) $value]);
    }
}
