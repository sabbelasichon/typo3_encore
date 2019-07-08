<?php


namespace Ssch\Typo3Encore\Asset;

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

interface VersionStrategyInterface
{

    /**
     * Returns the asset version for an asset.
     *
     * @param string $path A path
     *
     * @return string|null The version string|null
     */
    public function getVersion($path);

    /**
     * Applies version to the supplied path.
     *
     * @param string $path A path
     *
     * @return string|null The versionized path
     */
    public function applyVersion($path);
}
