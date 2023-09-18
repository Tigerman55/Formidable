<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\TestAsset;

use Formidable\FormDataTransferObjectInterface;

class SimpleDTO implements FormDataTransferObjectInterface
{
    public function __construct(public readonly string $foo, public readonly string $bar)
    {
    }

    public static function fromArrayOfArguments(array $arguments): FormDataTransferObjectInterface
    {
        return new self(...$arguments);
    }
}
