<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use FormError\FormErrorAssertion;
use Formidable\FormError\FormError;
use Formidable\FormError\FormErrorSequence;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\Exception\InvalidBindResultException;
use Formidable\Mapping\Exception\ValidBindResultException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Mapping\BindResult
 */
class BindResultTest extends TestCase
{
    public function testBindResultFromValue()
    {
        $bindResult = BindResult::fromValue('foo');
        self::assertTrue($bindResult->isSuccess());
        self::assertSame('foo', $bindResult->getValue());
        $this->expectException(ValidBindResultException::class);
        $bindResult->getFormErrorSequence();
    }

    public function testBindResultFromFormErrors()
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

    public function testBindResultFromFormErrorSequence()
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
