<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

use ArrayIterator;
use Traversable;

use function array_merge;

final class ValidationResult
{
    /** @var ValidationError[] */
    private array $validationErrors;

    public function __construct(ValidationError ...$validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    public function isSuccess(): bool
    {
        return empty($this->validationErrors);
    }

    public function merge(self $other): self
    {
        $validationResult                   = clone $this;
        $validationResult->validationErrors = array_merge($this->validationErrors, $other->validationErrors);
        return $validationResult;
    }

    /** @return Traversable<array-key, ValidationError> */
    public function getValidationErrors(): Traversable
    {
        return new ArrayIterator($this->validationErrors);
    }
}
