<?php

declare(strict_types=1);

namespace Formidable\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\BindResult;

final class IgnoredFormatter implements FormatterInterface
{
    public function __construct(private readonly mixed $value)
    {
    }

    public function bind(string $key, Data $data): BindResult
    {
        return BindResult::fromValue($this->value);
    }

    public function unbind(string $key, mixed $value): Data
    {
        return Data::none();
    }
}
