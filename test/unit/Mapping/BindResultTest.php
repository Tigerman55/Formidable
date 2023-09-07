<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use Formidable\FormError\FormError;
use Formidable\FormError\FormErrorSequence;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Exception\InvalidBindResultException;
use Formidable\Mapping\Exception\ValidBindResultException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Test\Unit\FormError\FormErrorAssertion;

#[CoversClass(BindResult::class)]
class BindResultTest extends TestCase
{
    #[Test]
    public function bindResultFromValue(): void
    {
        $bindResult = BindResult::fromValue('foo');
        self::assertTrue($bindResult->isSuccess());
        self::assertSame('foo', $bindResult->getValue());
        $this->expectException(ValidBindResultException::class);
        $bindResult->getFormErrorSequence();
    }

    #[Test]
    public function bindResultFromFormErrors(): void
    {
        $bindResult = BindResult::fromFormErrors(new FormError('foo', 'bar'));
        self::assertFalse($bindResult->isSuccess());
        FormErrorAssertion::assertErrorMessages(
            $this,
            $bindResult->getFormErrorSequence(),
            [
                'foo' => 'bar',
            ]
        );
        $this->expectException(InvalidBindResultException::class);
        $bindResult->getValue();
    }

    #[Test]
    public function bindResultFromFormErrorSequence(): void
    {
        $bindResult = BindResult::fromFormErrorSequence(new FormErrorSequence(new FormError('foo', 'bar')));
        self::assertFalse($bindResult->isSuccess());
        FormErrorAssertion::assertErrorMessages(
            $this,
            $bindResult->getFormErrorSequence(),
            [
                'foo' => 'bar',
            ]
        );
        $this->expectException(InvalidBindResultException::class);
        $bindResult->getValue();
    }
}
