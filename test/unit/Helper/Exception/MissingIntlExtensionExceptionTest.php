<?php

declare(strict_types=1);

namespace Test\Unit\Helper\Exception;

use Formidable\Helper\Exception\MissingIntlExtensionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MissingIntlExtensionException::class)]
class MissingIntlExtensionExceptionTest extends TestCase
{
    #[Test]
    public function fromMissingExtension(): void
    {
        self::assertSame(
            'You must install the PHP intl extension for this helper to work',
            MissingIntlExtensionException::fromMissingExtension()->getMessage()
        );
    }
}
