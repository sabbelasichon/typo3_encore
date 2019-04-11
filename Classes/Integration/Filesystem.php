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
}
