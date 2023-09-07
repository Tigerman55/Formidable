<?php

declare(strict_types=1);

namespace Formidable\Mapping;

use Formidable\Data;
use Formidable\Mapping\Formatter\FormatterInterface;

final class FieldMapping implements MappingInterface
{
    use MappingTrait;

    private FormatterInterface $binder;

    private string $key = '';

    public function __construct(FormatterInterface $binder)
    {
        $this->binder = $binder;
    }

    public function bind(Data $data): BindResult
    {
        $bindResult = $this->binder->bind($this->key, $data);

        if (! $bindResult->isSuccess()) {
            return $bindResult;
        }

        return $this->applyConstraints($bindResult->getValue(), $this->key);
    }

    public function unbind(mixed $value): Data
    {
        return $this->binder->unbind($this->key, $value);
    }

    public function withPrefixAndRelativeKey(string $prefix, string $relativeKey): MappingInterface
    {
        $clone      = clone $this;
        $clone->key = $this->createKeyFromPrefixAndRelativeKey($prefix, $relativeKey);
        return $clone;
    }
}
