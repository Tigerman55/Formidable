<?php

declare(strict_types=1);

namespace Formidable\Mapping;

use Closure;
use Formidable\Data;
use Formidable\FormError\FormErrorSequence;
use Formidable\Mapping\Exception\BindFailureException;
use Formidable\Mapping\Exception\InvalidMappingException;
use Formidable\Mapping\Exception\InvalidMappingKeyException;
use Formidable\Mapping\Exception\InvalidUnapplyResultException;
use Formidable\Mapping\Exception\MappedClassMismatchException;
use Formidable\Mapping\Exception\NonExistentMappedClassException;
use Formidable\Mapping\Exception\NonExistentUnapplyKeyException;
use Formidable\Mapping\Exception\UnbindFailureException;
use ReflectionClass;
use Throwable;

use function array_key_exists;
use function array_values;
use function class_exists;
use function is_array;
use function is_string;

final class ObjectMapping implements MappingInterface
{
    use MappingTrait;

    /** @var array<non-empty-string, MappingInterface> */
    private array $mappings = [];

    private string $key = '';

    /** @var Closure(mixed...): object  */
    private readonly Closure $apply;

    private readonly Closure $unapply;

    /**
     * @param class-string $className
     * @param Closure(mixed...): object|null $apply
     */
    public function __construct(
        array $mappings,
        private readonly string $className,
        ?Closure $apply = null,
        ?Closure $unapply = null
    ) {
        foreach ($mappings as $mappingKey => $mapping) {
            if (! is_string($mappingKey) || $mappingKey === '') {
                throw InvalidMappingKeyException::fromInvalidMappingKey($mappingKey);
            }

            if (! $mapping instanceof MappingInterface) {
                throw InvalidMappingException::fromInvalidMapping($mapping);
            }

            $this->mappings[$mappingKey] = $mapping->withPrefixAndRelativeKey($this->key, $mappingKey);
        }

        if (! class_exists($className)) {
            /** @psalm-suppress MixedArgument https://github.com/vimeo/psalm/issues/10215 */
            throw NonExistentMappedClassException::fromNonExistentClass($className);
        }

        $this->apply = $apply ?? function (mixed ...$arguments): object {
            return new $this->className(...array_values($arguments));
        };

        $this->unapply = $unapply ?? function (null|object $objInstance): array {
            if (! $objInstance instanceof $this->className) {
                throw MappedClassMismatchException::fromMismatchedClass($this->className, $objInstance);
            }

            $values          = [];
            $reflectionClass = new ReflectionClass($this->className);

            foreach ($reflectionClass->getProperties() as $property) {
                /** @var mixed */
                $values[$property->getName()] = $property->getValue($objInstance);
            }

            return $values;
        };
    }

    public function withMapping(string $key, MappingInterface $mapping): self
    {
        if ($key === '') {
            throw InvalidMappingKeyException::fromInvalidMappingKey($key);
        }

        $clone                 = clone $this;
        $clone->mappings[$key] = $mapping->withPrefixAndRelativeKey($clone->key, $key);

        return $clone;
    }

    public function bind(Data $data): BindResult
    {
        $arguments         = [];
        $formErrorSequence = new FormErrorSequence();

        foreach ($this->mappings as $key => $mapping) {
            try {
                $bindResult = $mapping->bind($data);
            } catch (Throwable $e) {
                throw BindFailureException::fromBindException($key, $e);
            }

            if (! $bindResult->isSuccess()) {
                $formErrorSequence = $formErrorSequence->merge($bindResult->getFormErrorSequence());
                continue;
            }

            /** @var mixed */
            $arguments[$key] = $bindResult->getValue();
        }

        if (! $formErrorSequence->isEmpty()) {
            return BindResult::fromFormErrorSequence($formErrorSequence);
        }

        $apply = $this->apply;
        $value = $apply(...array_values($arguments));

        if (! $value instanceof $this->className) {
            throw MappedClassMismatchException::fromMismatchedClass($this->className, $value);
        }

        return $this->applyConstraints($value, $this->key);
    }

    public function unbind(mixed $value): Data
    {
        $data    = Data::none();
        $unapply = $this->unapply;
        $values  = $unapply($value);

        if (! is_array($values)) {
            throw InvalidUnapplyResultException::fromInvalidUnapplyResult($values);
        }

        foreach ($this->mappings as $key => $mapping) {
            if (! array_key_exists($key, $values)) {
                throw NonExistentUnapplyKeyException::fromNonExistentUnapplyKey($key);
            }

            try {
                $data = $data->merge($mapping->unbind($values[$key]));
            } catch (Throwable $e) {
                throw UnbindFailureException::fromUnbindException($key, $e);
            }
        }

        return $data;
    }

    public function withPrefixAndRelativeKey(string $prefix, string $relativeKey): MappingInterface
    {
        $clone           = clone $this;
        $clone->key      = $this->createKeyFromPrefixAndRelativeKey($prefix, $relativeKey);
        $clone->mappings = [];

        foreach ($this->mappings as $mappingKey => $mapping) {
            $clone->mappings[$mappingKey] = $mapping->withPrefixAndRelativeKey($clone->key, $mappingKey);
        }

        return $clone;
    }
}
