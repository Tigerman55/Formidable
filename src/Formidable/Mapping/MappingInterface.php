<?php

declare(strict_types=1);

namespace Formidable\Mapping;

use Formidable\Data;
use Formidable\Mapping\Constraint\ConstraintInterface;

interface MappingInterface
{
    public function bind(Data $data): BindResult;

    public function unbind(mixed $value): Data;

    public function withPrefixAndRelativeKey(string $prefix, string $relativeKey): MappingInterface;

    public function verifying(ConstraintInterface ...$constraints): MappingInterface;
}
