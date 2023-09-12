<?php

declare(strict_types=1);

namespace Formidable\Mapping;

use Formidable\FormError\FormError;
use Formidable\FormError\FormErrorSequence;
use Formidable\Mapping\Exception\InvalidBindResultException;
use Formidable\Mapping\Exception\ValidBindResultException;

final class BindResult
{
    private mixed $value;

    private function __construct(private ?FormErrorSequence $formErrorSequence = null)
    {
    }

    public static function fromValue(mixed $value): self
    {
        $bindResult        = new self();
        $bindResult->value = $value;
        return $bindResult;
    }

    public static function fromFormErrors(FormError ...$formErrors): self
    {
        $bindResult                    = new self();
        $bindResult->formErrorSequence = new FormErrorSequence(...$formErrors);
        return $bindResult;
    }

    public static function fromFormErrorSequence(FormErrorSequence $formErrorSequence): self
    {
        $bindResult                    = new self();
        $bindResult->formErrorSequence = $formErrorSequence;
        return $bindResult;
    }

    public function isSuccess(): bool
    {
        return $this->formErrorSequence === null;
    }

    public function getValue(): mixed
    {
        if ($this->formErrorSequence !== null) {
            throw InvalidBindResultException::fromGetValueAttempt();
        }

        return $this->value;
    }

    public function getFormErrorSequence(): FormErrorSequence
    {
        if ($this->formErrorSequence === null) {
            throw ValidBindResultException::fromGetFormErrorsAttempt();
        }

        return $this->formErrorSequence;
    }
}
