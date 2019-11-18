<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Integration;

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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use UnexpectedValueException;

final class Filesystem implements FilesystemInterface
{
    public function get(string $pathToFile): string
    {
        $data = @file_get_contents($pathToFile);

        if (false === $data) {
            throw new UnexpectedValueException(sprintf('Data could not be read from file %s', $pathToFile));
        }

        return $data;
    }

    public function exists(string $pathToFile): bool
    {
        return file_exists($pathToFile);
    }

    public function getFileAbsFileName(string $pathToFile): string
    {
        return GeneralUtility::getFileAbsFileName($pathToFile);
    }

    public function getRelativeFilePath(string $pathToFile): string
    {
        $pathToFile = $this->getFileAbsFileName(($pathToFile));
        if (StringUtility::beginsWith($pathToFile, Environment::getPublicPath())) {
            return PathUtility::stripPathSitePrefix($pathToFile);
        }

        return $pathToFile;
    }
}
