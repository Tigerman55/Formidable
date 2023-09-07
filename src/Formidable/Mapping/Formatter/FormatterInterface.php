<?php

declare(strict_types=1);

namespace Formidable\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\BindResult;

interface FormatterInterface
{
    public function bind(string $key, Data $data): BindResult;

    public function unbind(string $key, mixed $value): Data;
}
