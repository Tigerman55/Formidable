<?php

declare(strict_types=1);

namespace Formidable\Mapping\Constraint;

interface ConstraintInterface
{
    public function __invoke(mixed $value): ValidationResult;
}
