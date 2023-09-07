<?php

declare(strict_types=1);

namespace Test\Unit\Helper;

use Formidable\FormError\FormError;
use Formidable\FormError\FormErrorSequence;
use Formidable\Helper\ErrorFormatter;
use Formidable\Helper\ErrorList;
use Formidable\Helper\Exception\InvalidHtmlAttributeKeyException;
use Formidable\Helper\Exception\InvalidHtmlAttributeValueException;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Formidable\Helper\ErrorList
 * @covers Formidable\Helper\AttributeTrait
 */
class ErrorListTest extends TestCase
{
    public function testRenderEmptyErrorSequence()
    {
        $helper = new ErrorList(new ErrorFormatter());
        $html   = $helper(new FormErrorSequence());

        self::assertSame('', $html);
    }

    public function testRenderMultipleErrors()
    {
        $helper = new ErrorList(new ErrorFormatter());
        $html   = $helper(new FormErrorSequence(new FormError('', 'error.required'), new FormError('', 'error.integer')));

        $this->assertXmlStringEqualsXmlString(
            '<ul><li>This field is required</li><li>Integer value expected</li></ul>',
            $html
        );
    }

    public function testRenderWithCustomAttributes()
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

    public function testExceptionOnInvalidAttributeKey()
    {
        $helper = new ErrorList(new ErrorFormatter());
        $this->expectException(InvalidHtmlAttributeKeyException::class);
        $helper(
            new FormErrorSequence(new FormError('', 'error.required')),
            [1 => 'test']
        );
    }

    public function testExceptionOnInvalidAttributeValue()
    {
        $helper = new ErrorList(new ErrorFormatter());
        $this->expectException(InvalidHtmlAttributeValueException::class);
        $helper(
            new FormErrorSequence(new FormError('', 'error.required')),
            ['test' => 1]
        );
    }
}
