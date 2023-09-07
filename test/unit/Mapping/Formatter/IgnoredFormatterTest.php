<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Formatter;

use Formidable\Data;
use Formidable\Mapping\Formatter\IgnoredFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(IgnoredFormatter::class)]
class IgnoredFormatterTest extends TestCase
{
    #[Test]
    public function bindValue(): void
    {
        self::assertSame(
            'foo',
            (new IgnoredFormatter('foo'))->bind('foo', Data::fromFlatArray(['foo' => 'baz']))->getValue()
        );
    }

    #[Test]
    public function unbindValue(): void
    {
        $data = (new IgnoredFormatter('foo'))->unbind('foo', 'bar');
        self::assertTrue($data->isEmpty());
    }
}
