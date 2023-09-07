<?php

declare(strict_types=1);

namespace Formidable\Mapping\Formatter;

use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;

use function is_int;
use function preg_match;

final class IntegerFormatter implements FormatterInterface
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

        if (! preg_match('(^-?[1-9]*\d+$)', $value)) {
            return BindResult::fromFormErrors(new FormError(
                $key,
                'error.integer'
            ));
        }

        return BindResult::fromValue((int) $data->getValue($key));
    }

    public function unbind(string $key, mixed $value): Data
    {
        if (! is_int($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'int');
        }

        return Data::fromFlatArray([$key => (string) $value]);
    }
}
