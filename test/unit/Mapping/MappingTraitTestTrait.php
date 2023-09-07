<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use Formidable\Mapping\Constraint\ConstraintInterface;
use Formidable\Mapping\MappingInterface;
use PHPUnit\Framework\Attributes\Test;

trait MappingTraitTestTrait
{
    #[Test]
    public function verifyingReturnsNewInstanceWithNewConstraints(): void
    {
        $mappingA = $this->getInstanceForTraitTests();
        $mappingB = $mappingA->verifying(self::createStub(ConstraintInterface::class));
        $mappingC = $mappingB->verifying(self::createStub(ConstraintInterface::class));

        self::assertNotSame($mappingA, $mappingB);
        self::assertNotSame($mappingB, $mappingC);
        $this->assertAttributeCount(0, 'constraints', $mappingA);
        $this->assertAttributeCount(1, 'constraints', $mappingB);
        $this->assertAttributeCount(2, 'constraints', $mappingC);
    }

    abstract protected function getInstanceForTraitTests(): MappingInterface;
}
