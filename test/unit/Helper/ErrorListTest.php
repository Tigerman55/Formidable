<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\FormError\FormError;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\AttributeTrait;
use Formidable\Helper\ErrorFormatter;
use Formidable\Helper\ErrorList;
use Formidable\Helper\Exception\InvalidHtmlAttributeKeyException;
use Formidable\Helper\Exception\InvalidHtmlAttributeValueException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ErrorList::class), CoversClass(AttributeTrait::class)]
class ErrorListTest extends TestCase
{
    #[Test]
    public function renderEmptyErrorSequence(): void
    {
        $helper = new ErrorList(new ErrorFormatter());
        $html   = $helper(new FormErrorSequence());

        self::assertSame('', $html);
    }

    #[Test]
    public function renderMultipleErrors(): void
    {
        $helper = new ErrorList(new ErrorFormatter());
        $html   = $helper(new FormErrorSequence(
            new FormError('', 'error.required'),
            new FormError('', 'error.integer')
        ));

        $this->assertXmlStringEqualsXmlString(
            '<ul><li>This field is required</li><li>Integer value expected</li></ul>',
            $html
        );
    }

    #[Test]
    public function renderWithCustomAttributes(): void
    {
        $helper = new ErrorList(new ErrorFormatter());
        $html   = $helper(
            new FormErrorSequence(new FormError('', 'error.required')),
            ['class' => 'errors', 'data-foo' => 'bar']
        );

        $this->assertXmlStringEqualsXmlString(
            '<ul class="errors" data-foo="bar"><li>This field is required</li></ul>',
            $html
        );
    }

    #[Test]
    public function exceptionOnInvalidAttributeKey(): void
    {
        $helper = new ErrorList(new ErrorFormatter());
        $this->expectException(InvalidHtmlAttributeKeyException::class);
        $helper(
            new FormErrorSequence(new FormError('', 'error.required')),
            [1 => 'test']
        );
    }

    #[Test]
    public function exceptionOnInvalidAttributeValue(): void
    {
        $helper = new ErrorList(new ErrorFormatter());
        $this->expectException(InvalidHtmlAttributeValueException::class);
        $helper(
            new FormErrorSequence(new FormError('', 'error.required')),
            ['test' => 1]
        );
    }
}
