<?php

declare(strict_types=1);

namespace Test\Unit\FormError;

use Formidable\FormError\FormError;
use Formidable\FormError\FormErrorSequence;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FormErrorSequence::class)]
class FormErrorSequenceTest extends TestCase
{
    #[Test]
    public function isEmptyReturnsTrueWithoutErrors(): void
    {
        self::assertTrue((new FormErrorSequence())->isEmpty());
    }

    #[Test]
    public function isEmptyReturnsFalseWithErrors(): void
    {
        self::assertFalse((new FormErrorSequence(new FormError('', '')))->isEmpty());
    }

    #[Test]
    public function countable(): void
    {
        self::assertCount(2, new FormErrorSequence(new FormError('', ''), new FormError('', '')));
    }

    #[Test]
    public function iterator(): void
    {
        $formErrorSequence = new FormErrorSequence(new FormError('foo', 'bar'), new FormError('baz', 'bat'));
        FormErrorAssertion::assertErrorMessages($this, $formErrorSequence, ['foo' => 'bar', 'baz' => 'bat']);
    }

    #[Test]
    public function collect(): void
    {
        self::assertCount(
            2,
            (new FormErrorSequence(
                new FormError('foo', ''),
                new FormError('bar', ''),
                new FormError('foo', '')
            ))->collect('foo')
        );
    }

    #[Test]
    public function merge(): void
    {
        $formErrorSequenceA = new FormErrorSequence(new FormError('foo', ''));
        $formErrorSequenceB = new FormErrorSequence(new FormError('bar', ''));
        $formErrorSequenceC = $formErrorSequenceA->merge($formErrorSequenceB);

        $this->assertNotSame($formErrorSequenceA, $formErrorSequenceC);
        $this->assertNotSame($formErrorSequenceB, $formErrorSequenceC);
        self::assertCount(2, $formErrorSequenceC);
    }
}
