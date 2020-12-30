<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

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
        self::assertTrue(is_string($idGenerator->generate()));
    }
}
