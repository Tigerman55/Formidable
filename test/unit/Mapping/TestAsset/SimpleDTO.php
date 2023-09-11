<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\TestAsset;

class SimpleDTO
{
    public function __construct(public readonly string $foo, public readonly string $bar)
    {
    }
}
