<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use Ssch\Typo3Encore\ValueObject\File;
use TYPO3\CMS\Core\SingletonInterface;

interface AssetRegistryInterface extends SingletonInterface
{
    public function registerFile(File $file): void;

    public function getRegisteredFiles(): array;

    public function getDefaultAttributes(): array;
}
