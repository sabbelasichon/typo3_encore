<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Form\FormDataProvider;

use Ssch\Typo3Encore\Asset\EntrypointLookupCollection;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class RichtextEncoreConfiguration implements FormDataProviderInterface
{
    private EntrypointLookupCollectionInterface $entrypointLookupCollection;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection = null)
    {
        $this->entrypointLookupCollection = $entrypointLookupCollection ?? GeneralUtility::makeInstance(
            EntrypointLookupCollection::class
        );
    }

    public function addData(array $result): array
    {
        foreach ($result['processedTca']['columns'] as $fieldName => $fieldConfig) {
            if (! isset($fieldConfig['config']['type']) || 'text' !== $fieldConfig['config']['type']) {
                continue;
            }

            if (! isset($fieldConfig['config']['enableRichtext']) || true !== (bool) $fieldConfig['config']['enableRichtext']) {
                continue;
            }

            $rteConfiguration = $fieldConfig['config']['richtextConfiguration'];

            // replace contentsCss with correct path
            if (! isset($rteConfiguration['editor']['config']['contentsCss'])) {
                continue;
            }
            $contentsCss = $rteConfiguration['editor']['config']['contentsCss'];
            if (! str_starts_with($contentsCss, 'typo3_encore:')) {
                continue;
            }

            // strip prefix
            $contentsCss = substr($contentsCss, strlen('typo3_encore:'));
            [$buildName, $entryName] = GeneralUtility::trimExplode(':', $contentsCss, true, 2);
            if ('' !== $entryName) {
                $entryName = $buildName;
                $buildName = EntrypointLookupInterface::DEFAULT_BUILD;
            }

            $entryPointLookup = $this->entrypointLookupCollection->getEntrypointLookup($buildName);
            $contentsCss = $entryPointLookup->getCssFiles($entryName);
            $result['processedTca']['columns'][$fieldName]['config']['richtextConfiguration']['editor']['config']['contentsCss'] = $contentsCss;
        }

        return $result;
    }
}
