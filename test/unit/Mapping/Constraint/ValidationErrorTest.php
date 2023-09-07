<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint;

use Formidable\Mapping\Constraint\ValidationError;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Constraint\ValidationError
 */
class ValidationErrorTest extends TestCase
{
    public function testMessageRetrieval()
    {
        self::assertSame('foo', (new ValidationError('foo'))->getMessage());
    }

    public function testArgumentsRetrieval()
    {
        self::assertSame(['foo'], (new ValidationError('', ['foo']))->getArguments());
    }

    public function testKeySuffixRetrieval()
    {
        self::assertSame('foo', (new ValidationError('', [], 'foo'))->getKeySuffix());
    }

    public function testDefaults()
    {
        $validationError = new ValidationError('');
        self::assertSame([], $validationError->getArguments());
        self::assertSame('', $validationError->getKeySuffix());
    }
}
