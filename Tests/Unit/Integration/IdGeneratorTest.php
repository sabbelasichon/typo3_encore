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

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Ssch\Typo3Encore\Integration\IdGenerator;

/**
 * @covers \Ssch\Typo3Encore\Integration\IdGenerator
 */
class IdGeneratorTest extends UnitTestCase
{
    /**
     * @test
     */
    public function idGeneratorReturnsString()
    {
        $idGenerator = new IdGenerator();
        $this->assertTrue(is_string($idGenerator->generate()));
    }
}
