<?php

declare(strict_types=1);

namespace Test\Unit\FormError;

use Formidable\FormError\FormErrorSequence;
use PHPUnit\Framework\TestCase;

class FormErrorAssertion
{
    public static function assertErrorMessages(
        TestCase $testCase,
        FormErrorSequence $formErrorSequence,
        array $expectedMessages
    ): void {
        $actualMessages = [];

        foreach ($formErrorSequence as $formError) {
            $actualMessages[$formError->getKey()] = $formError->getMessage();
        }

        $testCase::assertSame($expectedMessages, $actualMessages);
    }
}
