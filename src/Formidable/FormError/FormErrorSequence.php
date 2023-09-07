<?php

declare(strict_types=1);

namespace Formidable\FormError;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

use function array_filter;
use function array_merge;
use function count;

final class FormErrorSequence implements IteratorAggregate, Countable
{
    /** @var FormError[] */
    private $formErrors;

    public function __construct(FormError ...$formErrors)
    {
        $this->formErrors = $formErrors;
    }

    public function merge(self $other): self
    {
        return new self(...array_merge($this->formErrors, $other->formErrors));
    }

    public function collect(string $key): self
    {
        return new self(...array_filter($this->formErrors, function (FormError $formError) use ($key) {
            return $formError->getKey() === $key;
        }));
    }

    public function isEmpty(): bool
    {
        return empty($this->formErrors);
    }

    /** @return Traversable<array-key, FormError> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->formErrors);
    }

    public function count(): int
    {
        return count($this->formErrors);
    }
}
