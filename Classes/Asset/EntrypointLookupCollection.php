<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Asset;

use Ssch\Typo3Encore\Integration\EntryLookupFactoryInterface;

class EntrypointLookupCollection implements EntrypointLookupCollectionInterface
{
    private EntryLookupFactoryInterface $entryLookupFactory;

    /**
     * @var array|EntrypointLookupInterface[]
     */
    private ?array $buildEntrypoints = null;

    private ?string $defaultBuildName;

    public function __construct(EntryLookupFactoryInterface $entryLookupFactory, string $defaultBuildName = null)
    {
        $this->entryLookupFactory = $entryLookupFactory;
        $this->defaultBuildName = $defaultBuildName;
    }

    public function getEntrypointLookup(string $buildName = null): EntrypointLookupInterface
    {
        if (null === $this->buildEntrypoints) {
            $this->buildEntrypoints = $this->entryLookupFactory->getCollection();
        }
        if (null === $buildName) {
            if (null === $this->defaultBuildName) {
                throw new UndefinedBuildException(
                    'There is no default build configured: please pass an argument to getEntrypointLookup().'
                );
            }

            $buildName = $this->defaultBuildName;
        }

        if (! isset($this->buildEntrypoints[$buildName])) {
            throw new UndefinedBuildException(sprintf('The build "%s" is not configured', $buildName));
        }

        return $this->buildEntrypoints[$buildName];
    }
}
