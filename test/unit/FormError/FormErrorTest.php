<?php

declare(strict_types=1);

namespace Test\Unit\FormError;

use Formidable\FormError\FormError;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\FormError\FormError
 */
class FormErrorTest extends TestCase
{
    public function testKeyRetrieval()
    {
        self::assertSame('foo', (new FormError('foo', ''))->getKey());
    }

    public function testMessageRetrieval()
    {
        self::assertSame('foo', (new FormError('', 'foo'))->getMessage());
    }

    public function testArgumentsRetrieval()
    {
        self::assertSame(['foo' => 'bar'], (new FormError('', '', ['foo' => 'bar']))->getArguments());
    }
}
