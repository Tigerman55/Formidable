<?php

declare(strict_types=1);

namespace Formidable\Transformer;

use Closure;

final class CallbackTransformer implements TransformerInterface
{
    /** @param Closure(string, string): string $callback */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function __invoke(string $value, string $key): string
    {
        $callback = $this->callback;
        return $callback($value, $key);
    }
}
