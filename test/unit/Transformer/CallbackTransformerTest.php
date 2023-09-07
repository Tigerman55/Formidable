<?php

declare(strict_types=1);

namespace Test\Unit\Transformer;

use Formidable\Transformer\CallbackTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CallbackTransformer::class)]
class CallbackTransformerTest extends TestCase
{
    #[Test]
    public function transform(): void
    {
        $transformer = new CallbackTransformer(function (string $value, string $key): string {
            return $key . $value;
        });
        self::assertSame('foobar', $transformer('bar', 'foo'));
    }
}
