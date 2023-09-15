<?php

declare(strict_types=1);

namespace Formidable\Mapping;

use Formidable\FormError\FormError;
use Formidable\Mapping\Constraint\ConstraintInterface;
use Formidable\Mapping\Constraint\ValidationError;
use Formidable\Mapping\Constraint\ValidationResult;

use function array_map;
use function array_merge;
use function iterator_to_array;
use function preg_replace;

trait MappingTrait
{
    /** @var array<array-key, ConstraintInterface> */
    private array $constraints = [];

    public function verifying(ConstraintInterface ...$constraints): MappingInterface
    {
        $mapping              = clone $this;
        $mapping->constraints = array_merge($this->constraints, $constraints);
        return $mapping;
    }

    protected function applyConstraints(mixed $value, string $key): BindResult
    {
        $validationResult = new ValidationResult();

        foreach ($this->constraints as $constraint) {
            $validationResult = $validationResult->merge($constraint($value));
        }

        if ($validationResult->isSuccess()) {
            return BindResult::fromValue($value);
        }

        return BindResult::fromFormErrors(...array_map(
            static function (ValidationError $validationError) use ($key) {
                if ($key === '') {
                    $finalKey = $validationError->getKeySuffix();
                } elseif ($validationError->getKeySuffix() === '') {
                    $finalKey = $key;
                } else {
                    $finalKey = $key . preg_replace('(^[^\[]+)', '[\0]', $validationError->getKeySuffix());
                }

                return new FormError(
                    $finalKey,
                    $validationError->getMessage(),
                    $validationError->getArguments()
                );
            },
            iterator_to_array($validationResult->getValidationErrors())
        ));
    }

    protected function createKeyFromPrefixAndRelativeKey(string $prefix, string $relativeKey): string
    {
        if ($prefix === '') {
            return $relativeKey;
        }

        return $prefix . '[' . $relativeKey . ']';
    }
}
