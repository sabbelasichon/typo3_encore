<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Asset;

/**
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Ssch\Typo3Encore\Integration\EntryLookupFactoryInterface;

class EntrypointLookupCollection implements EntrypointLookupCollectionInterface
{
    /**
     * @var array|EntrypointLookupInterface[]
     */
    private array $buildEntrypoints;

    private ?string $defaultBuildName;

    public function __construct(EntryLookupFactoryInterface $entryLookupFactory, string $defaultBuildName = null)
    {
        $this->buildEntrypoints = $entryLookupFactory->getCollection();
        $this->defaultBuildName = $defaultBuildName;
    }

    /**
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
