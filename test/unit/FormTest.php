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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Test\Unit\Mapping\TestAsset\SimpleDTO;

use function iterator_to_array;

#[CoversClass(Form::class)]
class FormTest extends TestCase
{
    #[Test]
    public function withDefaults(): void
    {
        $data = Data::fromFlatArray(['foo' => 'bar']);

        $mapping = $this->createMock(MappingInterface::class);
        $mapping->expects(self::never())->method('bind');

        $form = (new Form($mapping))->withDefaults($data);

        self::assertFalse($form->hasErrors());
        self::assertSame('bar', $form->getField('foo')->value);
    }

    #[Test]
    public function bindValidData(): void
    {
        $data = Data::none();

        $mapping = $this->createMock(MappingInterface::class);
        $mapping->expects(self::once())
            ->method('bind')
            ->with($data)
            ->willReturn(BindResult::fromValue('foo'));

        $form = (new Form($mapping))->bind($data);

        self::assertFalse($form->hasErrors());
        self::assertSame('foo', $form->getValue());
    }

    #[Test]
    public function fill(): void
    {
        $formData    = new SimpleDTO('defaultFooValue', 'defaultBarValue');
        $formMapping = $this->createMock(MappingInterface::class);
        $formMapping->expects(self::once())
            ->method('unbind')
            ->willReturn(Data::fromFlatArray(['foo' => 'defaultFooValue']));

        $form = new Form($formMapping);
        $form = $form->fill($formData);
        self::assertSame('defaultFooValue', $form->getField('foo')->value);
    }

    #[Test]
    public function bindInvalidData(): void
    {
        $data = Data::none();

        $mapping = $this->createMock(MappingInterface::class);
        $mapping->expects(self::once())
            ->method('bind')
            ->with($data)
            ->willReturn(BindResult::fromFormErrors(new FormError('', 'foo')));

        $form = (new Form($mapping))->bind($data);

        self::assertTrue($form->hasErrors());
        self::assertSame('foo', iterator_to_array($form->getGlobalErrors())[0]->message);
        $this->expectException(InvalidDataException::class);
        $form->getValue();
    }

    #[Test]
    public function exceptionOnGetValueWithoutBoundData(): void
    {
        $form = new Form(self::createStub(MappingInterface::class));
        $this->expectException(UnboundDataException::class);
        $form->getValue();
    }

    #[Test]
    public function bindFromPostRequest(): void
    {
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('POST');
        $request->method('getParsedBody')->willReturn(['foo' => 'bar']);

        $form = (new Form($this->getSimpleMappingMock()))->bindFromRequest($request);

        self::assertFalse($form->hasErrors());
        self::assertSame('bar', $form->getValue());
    }

    public static function specialMethodProvider(): array
    {
        return [
            ['PATCH'],
            ['PUT'],
        ];
    }

    #[Test, DataProvider('specialMethodProvider')]
    public function bindFromPatchRequest(string $method): void
    {
        $stream = self::createStub(StreamInterface::class);
        $stream->method('__toString')->willReturn('foo=bar');

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);
        $request->method('getBody')->willReturn($stream);

        $form = (new Form($this->getSimpleMappingMock()))->bindFromRequest($request);

        self::assertFalse($form->hasErrors());
        self::assertSame('bar', $form->getValue());
    }

    #[Test]
    public function bindFromGetRequest(): void
    {
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getQueryParams')->willReturn(['foo' => 'bar']);

        $form = (new Form($this->getSimpleMappingMock()))->bindFromRequest($request);

        self::assertFalse($form->hasErrors());
        self::assertSame('bar', $form->getValue());
    }

    #[Test]
    public function bindFromRequestTrimsByDefault(): void
    {
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getQueryParams')->willReturn(['foo' => ' bar ']);

        (new Form($this->getSimpleMappingMock()))->bindFromRequest($request);
    }

    #[Test]
    public function trimForBindFromRequestCanBeDisabled(): void
    {
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('GET');
        $request->method('getQueryParams')->willReturn(['foo' => ' bar ']);

        $mapping = $this->createMock(MappingInterface::class);
        $mapping->expects(self::once())
            ->method('bind')
            ->with(self::callback(static fn (Data $data) => $data->hasKey('foo') && $data->getValue('foo') === ' bar '))
            ->willReturn(BindResult::fromValue(' bar '));

        (new Form($mapping))->bindFromRequest($request, false);
    }

    #[Test]
    public function withError(): void
    {
        $form = (
            new Form(self::createStub(MappingInterface::class))
        )->withError(new FormError('bar', 'foo'));
        self::assertTrue($form->hasErrors());
        self::assertSame('bar', iterator_to_array($form->getErrors())[0]->key);
        self::assertSame('foo', iterator_to_array($form->getErrors())[0]->message);
    }

    #[Test]
    public function withGlobalError(): void
    {
        $form = (new Form(self::createStub(MappingInterface::class)))->withGlobalError('foo');
        self::assertTrue($form->hasErrors());
        self::assertTrue($form->hasGlobalErrors());
        self::assertSame('', iterator_to_array($form->getGlobalErrors())[0]->key);
        self::assertSame('foo', iterator_to_array($form->getGlobalErrors())[0]->message);
    }

    #[Test]
    public function fieldRetrievalFromUnknownField(): void
    {
        $form  = new Form(self::createStub(MappingInterface::class));
        $field = $form->getField('foo');

        self::assertSame('foo', $field->key);
        self::assertSame('', $field->value);
        self::assertTrue($field->errors->isEmpty());
    }

    private function getSimpleMappingMock(): MappingInterface
    {
        $mapping = $this->createMock(MappingInterface::class);
        $mapping->expects(self::once())
            ->method('bind')
            ->with(self::callback(static fn(Data $data) => $data->hasKey('foo') && $data->getValue('foo') === 'bar'))
            ->willReturn(BindResult::fromValue('bar'));
        return $mapping;
    }

    private function getMockedMapping(
        string $key,
        ?string $value = null,
        ?Data $data = null,
        bool $success = true
    ): MappingInterface {
        $mapping = $this->createMock(MappingInterface::class);

        if ($value !== null) {
            $mapping->method('unbind')
                ->with($value)
                ->willReturn(Data::fromFlatArray([$key => $value]));
        }

        if ($value !== null && $data !== null) {
            $mapping->method('bind')
                ->with($data)
                ->willReturn($success
                ? BindResult::fromValue($value)
                : BindResult::fromFormErrors(new FormError($key, $value)));
        }

        $mapping->method('withPrefixAndRelativeKey')->with('', $key)->willReturn($mapping);

        return $mapping;
    }
}
