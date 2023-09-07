<?php

declare(strict_types=1);

namespace Formidable\Transformer;

use Closure;

final class CallbackTransformer implements TransformerInterface
{
    private Closure $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke(string $value, string $key): string
    {
        $callback = $this->callback;
        return $callback($value, $key);
    }
}
