<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

final class ValidationError
{
    public function __construct(
        private readonly string $message,
        private readonly array $arguments = [],
        private readonly string $keySuffix = ''
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getKeySuffix(): string
    {
        return $this->keySuffix;
    }
}
