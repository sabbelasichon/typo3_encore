<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Asset;

interface EntrypointLookupInterface
{
    /**
     * @var string
     */
    public const DEFAULT_BUILD = '_default';

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
