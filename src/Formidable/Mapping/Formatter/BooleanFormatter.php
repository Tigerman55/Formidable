<?php

declare(strict_types=1);

namespace Formidable\Mapping\Formatter;

use Formidable\Data;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Formatter\Exception\InvalidTypeException;

use function is_bool;

final class BooleanFormatter implements FormatterInterface
{
    public function bind(string $key, Data $data): BindResult
    {
        return match ($data->getValue($key, 'false')) {
            'true' => BindResult::fromValue(true),
            'false' => BindResult::fromValue(false),
            default => BindResult::fromFormErrors(new FormError(
                $key,
                'error.boolean'
            )),
        };
    }

    public function unbind(string $key, mixed $value): Data
    {
        if (! is_bool($value)) {
            throw InvalidTypeException::fromInvalidType($value, 'bool');
        }

        return Data::fromFlatArray([$key => $value ? 'true' : 'false']);
    }
}
