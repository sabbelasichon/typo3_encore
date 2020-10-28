<?php


namespace Ssch\Typo3Encore\Integration;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;

interface AssetRegistryInterface extends SingletonInterface
{
    public function registerFile(string $file, string $type, array $attributes = [], string $rel = 'preload'): void;

    public function getRegisteredFiles(): array;

    public function getDefaultAttributes(): array;
}
