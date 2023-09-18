<?php

declare(strict_types=1);

namespace Formidable;

use Formidable\Exception\InvalidKeyException;
use Formidable\Exception\InvalidValueException;
use Formidable\Exception\NonExistentKeyException;
use Formidable\Transformer\TransformerInterface;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_reduce;
use function array_unique;
use function count;
use function is_array;
use function is_string;
use function preg_match;
use function preg_quote;

use const ARRAY_FILTER_USE_BOTH;
use const ARRAY_FILTER_USE_KEY;

final class Data
{
    /** @param array<string, string> $data */
    private function __construct(private array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function none(): self
    {
        return new self([]);
    }

    public static function fromFlatArray(array $flatArray): self
    {
        $originalCount = count($flatArray);

        if ($originalCount > count(array_filter($flatArray, 'is_string', ARRAY_FILTER_USE_KEY))) {
            throw InvalidKeyException::fromArrayWithNonStringKeys();
        }

        if ($originalCount > count(array_filter($flatArray, 'is_string'))) {
            throw InvalidValueException::fromArrayWithNonStringValues($flatArray);
        }

        /** @var array<string, string> $flatArray */
        return new self($flatArray);
    }

    public static function fromNestedArray(array $nestedArray): self
    {
        return new self(self::flattenNestedArray($nestedArray));
    }

    public function merge(self $data): self
    {
        $newData        = clone $this;
        $newData->data += $data->data;

        return $newData;
    }

    public function filter(callable $filter): self
    {
        $newData       = clone $this;
        $newData->data = array_filter($newData->data, $filter, ARRAY_FILTER_USE_BOTH);

        return $newData;
    }

    public function transform(TransformerInterface $transformer): self
    {
        $data = [];

        foreach ($this->data as $key => $value) {
            $data[$key] = $transformer($value, $key);
        }

        return new self($data);
    }

    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function getValue(string $key, ?string $fallback = null): string
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if ($fallback !== null) {
            return $fallback;
        }

        throw NonExistentKeyException::fromNonExistentKey($key);
    }

    /** @return array<string> */
    public function getIndexes(string $key): array
    {
        /** @var array<string> $indexes */
        $indexes = array_unique(
            array_reduce(
                array_keys($this->data),
                static function (array $indexes, string $currentKey) use ($key) {
                    if (preg_match('(^' . preg_quote($key, '/') . '\[(?<index>[^]]+)])', $currentKey, $matches)) {
                        $indexes[] = $matches['index'];
                    }

                    return $indexes;
                },
                []
            )
        );
        return $indexes;
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * @param array<array-key, mixed> $nestedArray
     * @return array<string, string>
     */
    private static function flattenNestedArray(array $nestedArray, string $prefix = ''): array
    {
        $flatArray = [];

        foreach ($nestedArray as $key => $value) {
            if (! is_string($value) && ! is_array($value)) {
                throw InvalidValueException::fromNonNestedValue($value);
            }

            if (! is_string($key) && $prefix === '') {
                throw InvalidKeyException::fromNonNestedKey();
            }

            if ($prefix !== '') {
                $key = $prefix . '[' . $key . ']';
            }

            if (is_string($value)) {
                $flatArray[$key] = $value;
                continue;
            }

            $flatArray += self::flattenNestedArray($value, $key);
        }

        return $flatArray;
    }
}
