<?php

declare(strict_types=1);

namespace Formidable\Mapping;

use Formidable\Data;

use function str_starts_with;

final class OptionalMapping implements MappingInterface
{
    use MappingTrait;

    private MappingInterface $wrappedMapping;

    private string $key = '';

    public function __construct(MappingInterface $wrappedMapping)
    {
        $this->wrappedMapping = $wrappedMapping;
    }

    public function bind(Data $data): BindResult
    {
        if (
            ! $data->filter(function (string $value, string $key) {
                if ($key !== $this->key && ! str_starts_with($key, $this->key . '[')) {
                    return false;
                }

                if ($value === '') {
                    return false;
                }

                return true;
            })->isEmpty()
        ) {
            $bindResult = $this->wrappedMapping->bind($data);

            if ($bindResult->isSuccess()) {
                return $this->applyConstraints($bindResult->getValue(), $this->key);
            }

            return $bindResult;
        }

        return $this->applyConstraints(null, $this->key);
    }

    public function unbind(mixed $value): Data
    {
        if ($value === null) {
            return Data::none();
        }

        return $this->wrappedMapping->unbind($value);
    }

    public function withPrefixAndRelativeKey(string $prefix, string $relativeKey): MappingInterface
    {
        $clone                 = clone $this;
        $clone->key            = $this->createKeyFromPrefixAndRelativeKey($prefix, $relativeKey);
        $clone->wrappedMapping = $clone->wrappedMapping->withPrefixAndRelativeKey($prefix, $relativeKey);
        return $clone;
    }
}
