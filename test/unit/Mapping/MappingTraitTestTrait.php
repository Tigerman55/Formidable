<?php

declare(strict_types=1);

namespace Test\Unit\Mapping;

use Formidable\Mapping\Constraint\ConstraintInterface;
use Formidable\Mapping\MappingInterface;

trait MappingTraitTestTrait
{
    #[Test]
    public function verifyingReturnsNewInstanceWithNewConstraints(): void
    {
        $mappingA = $this->getInstanceForTraitTests();
        $mappingB = $mappingA->verifying($this->prophesize(ConstraintInterface::class)->reveal());
        $mappingC = $mappingB->verifying($this->prophesize(ConstraintInterface::class)->reveal());

        $this->assertNotSame($mappingA, $mappingB);
        $this->assertNotSame($mappingB, $mappingC);
        $this->assertAttributeCount(0, 'constraints', $mappingA);
        $this->assertAttributeCount(1, 'constraints', $mappingB);
        $this->assertAttributeCount(2, 'constraints', $mappingC);
    }

    abstract protected function getInstanceForTraitTests(): MappingInterface;
}
