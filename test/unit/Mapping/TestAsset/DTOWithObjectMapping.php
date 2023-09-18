<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\TestAsset;

use Formidable\FormDataTransferObjectInterface;

final class DTOWithObjectMapping implements FormDataTransferObjectInterface
{
    public function __construct(public readonly SimpleDTO $foo)
    {
    }

    public static function fromArrayOfArguments(array $arguments): FormDataTransferObjectInterface
    {
        return new self(...$arguments);
    }
}
