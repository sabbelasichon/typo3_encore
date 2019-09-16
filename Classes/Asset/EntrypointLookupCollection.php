<?php

declare(strict_types = 1);

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

use Ssch\Typo3Encore\Integration\EntryLookupFactoryInterface;

class EntrypointLookupCollection implements EntrypointLookupCollectionInterface
{
    /**
     * @var array|EntrypointLookupInterface[]
     */
    private $buildEntrypoints;

    /**
     * @var string
     */
    private $defaultBuildName;

    public function __construct(EntryLookupFactoryInterface $entryLookupFactory, string $defaultBuildName = null)
    {
        $this->buildEntrypoints = $entryLookupFactory->getCollection();
        $this->defaultBuildName = $defaultBuildName;
    }

    /**
     * @param string|null $buildName
     *
     * @return EntrypointLookupInterface
     * @throws UndefinedBuildException
     */
    public function getEntrypointLookup(string $buildName = null): EntrypointLookupInterface
    {
        if (null === $buildName) {
            if (null === $this->defaultBuildName) {
                throw new UndefinedBuildException('There is no default build configured: please pass an argument to getEntrypointLookup().');
            }

            $buildName = $this->defaultBuildName;
        }

        if (!isset($this->buildEntrypoints[$buildName])) {
            throw new UndefinedBuildException(sprintf('The build "%s" is not configured', $buildName));
        }

        return $this->buildEntrypoints[$buildName];
    }
}
