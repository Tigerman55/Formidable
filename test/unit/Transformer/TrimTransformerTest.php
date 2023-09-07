<?php

declare(strict_types=1);

namespace Test\Unit\Transformer;

use Formidable\Transformer\TrimTransformer;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Transformer\TrimTransformer
 */
class TrimTransformerTest extends TestCase
{
    public function testTransform()
    {
        $transformer = new TrimTransformer();
        self::assertSame('foo', $transformer("\0\r\n foo\0\r\n ", ''));
    }
}
