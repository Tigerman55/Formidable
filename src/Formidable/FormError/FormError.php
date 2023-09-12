<?php

declare(strict_types=1);

namespace Formidable\FormError;

final class FormError
{
    public function __construct(
        private readonly string $key,
        private readonly string $message,
        private readonly array $arguments = []
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}
