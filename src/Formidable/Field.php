<?php

declare(strict_types=1);

namespace Formidable;

use Formidable\FormError\FormErrorSequence;

final class Field
{
    private string $key;

    private string $value;

    private FormErrorSequence $errors;

    private Data $data;

    public function __construct(string $key, string $value, FormErrorSequence $errors, Data $data)
    {
        $this->key    = $key;
        $this->value  = $value;
        $this->errors = $errors;
        $this->data   = $data;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getErrors(): FormErrorSequence
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return ! $this->errors->isEmpty();
    }

    /**
     * @return string[]
     */
    public function getIndexes(): array
    {
        return $this->data->getIndexes($this->key);
    }

    /**
     * @return string[]
     */
    public function getNestedValues(bool $preserveKeys = false): array
    {
        $values = [];

        foreach ($this->getIndexes() as $index) {
            $key = $this->key . '[' . $index . ']';

            if ($this->data->hasKey($key)) {
                if ($preserveKeys) {
                    $values[$index] = $this->data->getValue($key);
                } else {
                    $values[] = $this->data->getValue($key);
                }
            }
        }

        return $values;
    }
}
