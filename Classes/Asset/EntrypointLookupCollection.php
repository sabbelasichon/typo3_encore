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
    /**
     * @var array|EntrypointLookupInterface[]
     */
    private ?array $buildEntrypoints = null;

    public function __construct(
        private readonly EntryLookupFactoryInterface $entryLookupFactory,
        private readonly ?string $defaultBuildName = null
    ) {
    }

    public function getEntrypointLookup(string $buildName = null): EntrypointLookupInterface
    {
        if ($this->buildEntrypoints === null) {
            $this->buildEntrypoints = $this->entryLookupFactory->getCollection();
        }
        if ($buildName === null) {
            if ($this->defaultBuildName === null) {
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
