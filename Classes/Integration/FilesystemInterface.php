<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

interface FilesystemInterface
{
    public function get(string $pathToFile): string;

    public function exists(string $pathToFile): bool;

    public function getFileAbsFileName(string $pathToFile): string;

    public function createHash(string $entrypointJsonPath): string;
}
