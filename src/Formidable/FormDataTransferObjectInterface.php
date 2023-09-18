<?php

declare(strict_types=1);

namespace Formidable;

interface FormDataTransferObjectInterface
{
    /** @param list<mixed> $arguments */
    public static function fromArrayOfArguments(array $arguments): self;
}
