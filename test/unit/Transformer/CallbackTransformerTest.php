<?php

declare(strict_types=1);

namespace Test\Unit\Transformer;

use Formidable\Transformer\CallbackTransformer;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Transformer\CallbackTransformer
 */
class CallbackTransformerTest extends TestCase
{
    public function testTransform()
    {
        $transformer = new CallbackTransformer(function (string $value, string $key): string {
            return $key . $value;
        });
        self::assertSame('foobar', $transformer('bar', 'foo'));
    }
}
