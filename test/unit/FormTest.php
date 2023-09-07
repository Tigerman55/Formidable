<?php

declare(strict_types=1);

namespace Test\Unit;

use Formidable\Data;
use Formidable\Exception\InvalidDataException;
use Formidable\Exception\UnboundDataException;
use Formidable\Form;
use Formidable\FormError\FormError;
use Formidable\Mapping\BindResult;
use Formidable\Mapping\MappingInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

use function iterator_to_array;

/**
 * @covers Formidable\Form
 */
class FormTest extends TestCase
{
    public function testWithDefaults()
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind()->shouldNotBeCalled();

        $form = (new Form($mapping->reveal()))->withDefaults($data);

        self::assertFalse($form->hasErrors());
        self::assertSame('bar', $form->getField('foo')->getValue());
    }

    public function testBindValidData()
    {
        $data = Data::none();

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind($data)->willReturn(BindResult::fromValue('foo'))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bind($data);

        self::assertFalse($form->hasErrors());
        self::assertSame('foo', $form->getValue());
    }

    public function testFill()
    {
        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->unbind(['foo' => 'bar'])->willReturn(Data::fromFlatArray(['foo' => 'bar']))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->fill(['foo' => 'bar']);
        self::assertSame('bar', $form->getField('foo')->getValue());
    }

    public function testBindInvalidData()
    {
        $data = Data::none();

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind($data)->willReturn(BindResult::fromFormErrors(new FormError('', 'foo')))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bind($data);

        self::assertTrue($form->hasErrors());
        self::assertSame('foo', iterator_to_array($form->getGlobalErrors())[0]->getMessage());
        $this->expectException(InvalidDataException::class);
        $form->getValue();
    }

    public function testExceptionOnGetValueWithoutBoundData()
    {
        $form = new Form($this->prophesize(MappingInterface::class)->reveal());
        $this->expectException(UnboundDataException::class);
        $form->getValue();
    }

    public function testBindFromPostRequest()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('POST');
        $request->getParsedBody()->willReturn(['foo' => 'bar']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && 'bar' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue('bar'))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bindFromRequest($request->reveal());

        self::assertFalse($form->hasErrors());
        self::assertSame('bar', $form->getValue());
    }

    public function specialMethodProvider(): array
    {
        return [
            ['PATCH'],
            ['PUT'],
        ];
    }

    /**
     * @dataProvider specialMethodProvider
     */
    public function testBindFromPatchRequest(string $method)
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->__toString()->willReturn('foo=bar');

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn($method);
        $request->getBody()->willReturn($stream->reveal());

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && 'bar' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue('bar'))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bindFromRequest($request->reveal());

        self::assertFalse($form->hasErrors());
        self::assertSame('bar', $form->getValue());
    }

    public function testBindFromGetRequest()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('GET');
        $request->getQueryParams()->willReturn(['foo' => 'bar']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && 'bar' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue('bar'))->shouldBeCalled();

        $form = (new Form($mapping->reveal()))->bindFromRequest($request->reveal());

        self::assertFalse($form->hasErrors());
        self::assertSame('bar', $form->getValue());
    }

    public function testBindFromRequestTrimsByDefault()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('GET');
        $request->getQueryParams()->willReturn(['foo' => ' bar ']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && 'bar' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue('bar'))->shouldBeCalled();

        (new Form($mapping->reveal()))->bindFromRequest($request->reveal());
    }

    public function testTrimForBindFromRequestCanBeDisabled()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('GET');
        $request->getQueryParams()->willReturn(['foo' => ' bar ']);

        $mapping = $this->prophesize(MappingInterface::class);
        $mapping->bind(Argument::that(function (Data $data) {
            return $data->hasKey('foo') && ' bar ' === $data->getValue('foo');
        }))->willReturn(BindResult::fromValue(' bar '))->shouldBeCalled();

        (new Form($mapping->reveal()))->bindFromRequest($request->reveal(), false);
    }

    public function testWithError()
    {
        $form = (
            new Form($this->prophesize(MappingInterface::class)->reveal())
        )->withError(new FormError('bar', 'foo'));
        self::assertTrue($form->hasErrors());
        self::assertSame('bar', iterator_to_array($form->getErrors())[0]->getKey());
        self::assertSame('foo', iterator_to_array($form->getErrors())[0]->getMessage());
    }

    public function testWithGlobalError()
    {
        $form = (new Form($this->prophesize(MappingInterface::class)->reveal()))->withGlobalError('foo');
        self::assertTrue($form->hasErrors());
        self::assertTrue($form->hasGlobalErrors());
        self::assertSame('', iterator_to_array($form->getGlobalErrors())[0]->getKey());
        self::assertSame('foo', iterator_to_array($form->getGlobalErrors())[0]->getMessage());
    }

    public function testFieldRetrivalFromUnknownField()
    {
        $form  = new Form($this->prophesize(MappingInterface::class)->reveal());
        $field = $form->getField('foo');

        self::assertSame('foo', $field->getKey());
        self::assertSame('', $field->getValue());
        self::assertTrue($field->getErrors()->isEmpty());
    }
}
