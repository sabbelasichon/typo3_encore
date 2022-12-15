<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

final class FixedIdGenerator implements IdGeneratorInterface
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function generate(): string
    {
        return $this->id;
    }
}
