<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\InvalidSelectLabelException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidSelectLabelException::class)]
class InvalidSelectLabelExceptionTest extends TestCase
{
    #[Test]
    public function fromInvalidLabel(): void
    {
        self::assertSame(
            'Label must either be a string or an array of child values, but got integer',
            InvalidSelectLabelException::fromInvalidLabel(1)->getMessage()
        );
    }
}
