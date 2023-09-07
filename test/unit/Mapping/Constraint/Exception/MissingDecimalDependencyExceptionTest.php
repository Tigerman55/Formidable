<?php

declare(strict_types=1);

namespace Test\Unit\Mapping\Constraint\Exception;

use Formidable\Mapping\Constraint\Exception\MissingDecimalDependencyException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\Constraint\Exception\MissingDecimalDependencyException
 */
class MissingDecimalDependencyExceptionTest extends TestCase
{
    public function testFromMissingDependency()
    {
        self::assertSame(
            'You must composer require litipk/php-bignumbers for this constraint to work',
            MissingDecimalDependencyException::fromMissingDependency()->getMessage()
        );
    }
}
