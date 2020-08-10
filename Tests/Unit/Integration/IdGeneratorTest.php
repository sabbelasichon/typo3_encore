<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

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
