<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Exception;

use Formidable\Mapping\Exception\InvalidUnapplyResultException;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

/**
 * @covers Formidable\Mapping\Exception\InvalidUnapplyResultException
 */
class InvalidUnapplyResultExceptionTest extends TestCase
{
    public function testFromInvalidUnapplyResult()
    {
        self::assertSame(
            'Unapply was expected to return an array, but returned object',
            InvalidUnapplyResultException::fromInvalidUnapplyResult(new stdClass())->getMessage()
        );
    }
}
