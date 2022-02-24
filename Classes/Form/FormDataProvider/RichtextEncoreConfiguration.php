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

            // https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html#cfg-contentsCss
            // contentCss could be an array or string. We only work with arrays
            $contentsCss = (array) $contentsCss;

            $updatedContentCss = [];
            foreach ($contentsCss as $cssFile) {
                if (! str_starts_with($cssFile, 'typo3_encore:')) {
                    // keep the css file as-is
                    $updatedContentCss[] = $cssFile;
                    continue;
                }

                // strip prefix
                $cssFile = substr($cssFile, strlen('typo3_encore:'));
                $buildAndEntryName = GeneralUtility::trimExplode(':', $cssFile, true, 2);
                $buildName = EntrypointLookupInterface::DEFAULT_BUILD;

                if (2 === count($buildAndEntryName)) {
                    [$buildName, $entryName] = $buildAndEntryName;
                } else {
                    $entryName = $buildAndEntryName[0];
                }

                $entryPointLookup = $this->entrypointLookupCollection->getEntrypointLookup($buildName);
                $cssFiles = $entryPointLookup->getCssFiles($entryName);
                $updatedContentCss = array_merge($updatedContentCss, $cssFiles);
                // call reset() to allow multiple RTEs on the same page.
                // Otherwise only the first RTE will have the CSS.
                $entryPointLookup->reset();
            }
            $result['processedTca']['columns'][$fieldName]['config']['richtextConfiguration']['editor']['config']['contentsCss'] = $updatedContentCss;
        }

        return $result;
    }
}
