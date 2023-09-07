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

    private ?FormErrorSequence $formErrorSequence;

    private function __construct()
    {
        $this->formErrorSequence = null;
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
        return null === $this->formErrorSequence;
    }

    public function getValue(): mixed
    {
        if (null !== $this->formErrorSequence) {
            throw InvalidBindResultException::fromGetValueAttempt();
        }

        return $this->value;
    }

    public function getFormErrorSequence(): FormErrorSequence
    {
        if (null === $this->formErrorSequence) {
            throw ValidBindResultException::fromGetFormErrorsAttempt();
        }

        return $this->formErrorSequence;
    }
}
