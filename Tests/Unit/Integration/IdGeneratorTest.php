<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

use Ssch\Typo3Encore\Integration\IdGenerator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class IdGeneratorTest extends UnitTestCase
{
    public function testIdGeneratorReturnsString(): void
    {
        $idGenerator = new IdGenerator();
        self::assertTrue(is_string($idGenerator->generate()));
    }
}
