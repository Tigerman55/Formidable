<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\TestAsset;

final class DTOWithObjectMapping
{
    public function __construct(public readonly SimpleDTO $foo)
    {
    }
}
