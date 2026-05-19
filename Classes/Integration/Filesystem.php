<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Integration;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use UnexpectedValueException;

final class Filesystem implements FilesystemInterface
{
    public function get(string $pathToFile): string
    {
        $data = GeneralUtility::getUrl($pathToFile);

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
    public function createHash(string $entrypointJsonPath): string
    {
        $md5HashOfFile = md5_file($entrypointJsonPath);

        if (false === $md5HashOfFile) {
            $message = sprintf('File "%s" could not be md5Hashed', $entrypointJsonPath);
            throw new UnexpectedValueException($message);
        }

        return $md5HashOfFile;
    }
}
