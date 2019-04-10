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

interface EntrypointLookupInterface
{

    /**
     * @param string $entryName
     *
     * @return array
     */
    public function getJavaScriptFiles(string $entryName): array;

    /**
     * @param string $entryName
     *
     * @return array
     */
    public function getCssFiles(string $entryName): array;

    /**
     * Resets the state of this service.
     */
    public function reset();
}
