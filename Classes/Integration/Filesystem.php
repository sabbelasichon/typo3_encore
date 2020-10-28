<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Integration;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
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

    /**
     * @codeCoverageIgnore
     */
    public function getFileAbsFileName(string $pathToFile): string
    {
        return GeneralUtility::getFileAbsFileName($pathToFile);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getRelativeFilePath(string $pathToFile): string
    {
        $pathToFile = $this->getFileAbsFileName(($pathToFile));
        if (StringUtility::beginsWith($pathToFile, Environment::getPublicPath())) {
            return PathUtility::stripPathSitePrefix($pathToFile);
        }

        return $pathToFile;
    }

    /**
     * @codeCoverageIgnore
     */
    public function createHash(string $entrypointJsonPath): string
    {
        return md5_file($entrypointJsonPath);
    }
}
