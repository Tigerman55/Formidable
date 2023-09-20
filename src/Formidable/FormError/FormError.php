<?php

declare(strict_types=1);

namespace Formidable\FormError;

final class FormError
{
    public function __construct(
        public readonly string $key,
        public readonly string $message,
        public readonly array $arguments = []
    ) {
    }
}
