<?php

namespace Ssch\Typo3Encore\Asset;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
interface EntrypointLookupCollectionInterface
{
    /**
     * Retrieve the EntrypointLookupInterface for the given build.
     *
     * @param string|null $buildName
     *
     * @return EntrypointLookupInterface
     */
    public function getEntrypointLookup(string $buildName = null): EntrypointLookupInterface;
}
