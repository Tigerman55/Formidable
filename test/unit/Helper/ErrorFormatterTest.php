<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\Helper\ErrorFormatter;
use Formidable\Helper\Exception\NonExistentMessageException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ErrorFormatter::class)]
class ErrorFormatterTest extends TestCase
{
    #[Test, DataProvider('builtInMessageProvider')]
    public function builtInMessages(string $key, string $expectedMessage, array $arguments = []): void
    {
        $errorFormatter = new ErrorFormatter();
        $message        = $errorFormatter($key, $arguments);

        self::assertSame($expectedMessage, $message);
    }

    public static function builtInMessageProvider(): array
    {
        return [
            'error.required'            => ['error.required', 'This field is required'],
            'error.empty'               => ['error.empty', 'Value must not be empty'],
            'error.integer'             => ['error.integer', 'Integer value expected'],
            'error.float'               => ['error.float', 'Float value expected'],
            'error.boolean'             => ['error.boolean', 'Boolean value expected'],
            'error.date'                => ['error.date', 'Date value expected'],
            'error.time'                => ['error.time', 'Time value expected'],
            'error.date-time'           => ['error.date-time', 'Datetime value expected'],
            'error.email-address'       => ['error.email-address', 'Valid email address required'],
            'error.min-length.singular' => ['error.min-length', 'Minimum length is 1 character', ['lengthLimit' => 1]],
            'error.min-length.plural'   => ['error.min-length', 'Minimum length is 2 characters', ['lengthLimit' => 2]],
            'error.max-length.singular' => ['error.max-length', 'Maximum length is 1 character', ['lengthLimit' => 1]],
            'error.max-length.plural'   => ['error.max-length', 'Maximum length is 2 characters', ['lengthLimit' => 2]],
            'error.min-number'          => ['error.min-number', 'Minimum value is 1.5', ['limit' => '1.500']],
            'error.max-number'          => ['error.max-number', 'Maximum value is 3.5', ['limit' => '3.500']],
            'error.step-number'         => [
                'error.step-number',
                'Value is invalid, closest valid values are 3.5 and 4.5',
                [
                    'lowValue'  => '3.500',
                    'highValue' => '4.500',
                ],
            ],
        ];
    }

    #[Test]
    public function overrideBuiltInMessage(): void
    {
        $errorFormatter = new ErrorFormatter(['error.required' => 'foo']);
        self::assertSame('foo', $errorFormatter('error.required'));
    }

    #[Test]
    public function customMessage(): void
    {
        $errorFormatter = new ErrorFormatter(['error.foo' => 'bar']);
        self::assertSame('bar', $errorFormatter('error.foo'));
    }

    #[Test]
    public function exceptionOnNonExistentMessage(): void
    {
        $errorFormatter = new ErrorFormatter();
        $this->expectException(NonExistentMessageException::class);
        $errorFormatter('error.foo');
    }
}
