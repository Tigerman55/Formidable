<?php

declare(strict_types=1);

namespace Test\Unit\Transformer;

use Formidable\Transformer\TrimTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TrimTransformer::class)]
class TrimTransformerTest extends TestCase
{
    #[Test]
    public function transform(): void
    {
        $transformer = new TrimTransformer();
        self::assertSame('foo', $transformer("\0\r\n foo\0\r\n ", ''));
    }
}
