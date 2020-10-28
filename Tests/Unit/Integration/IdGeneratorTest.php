<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

use Ssch\Typo3Encore\Integration\IdGenerator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Ssch\Typo3Encore\Integration\IdGenerator
 */
final class IdGeneratorTest extends UnitTestCase
{
    /**
     * @test
     */
    public function idGeneratorReturnsString(): void
    {
        $idGenerator = new IdGenerator();
        $this->assertTrue(is_string($idGenerator->generate()));
    }
}
