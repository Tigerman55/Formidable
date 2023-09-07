<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

final class ValidationError
{
    private string $message;

    private array $arguments;

    private string $keySuffix;

    public function __construct(string $message, array $arguments = [], string $keySuffix = '')
    {
        $this->message   = $message;
        $this->arguments = $arguments;
        $this->keySuffix = $keySuffix;
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
