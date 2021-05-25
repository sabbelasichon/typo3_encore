<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Asset;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

final class TagRenderer implements TagRendererInterface
{
    /**
     * @var EntrypointLookupCollectionInterface
     */
    private $entrypointLookupCollection;

    /**
     * @var AssetRegistryInterface
     */
    private $assetRegistry;

    /**
     * @var ApplicationType|null
     */
    private $applicationType;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookupCollection, AssetRegistryInterface $assetRegistry)
    {
        try {
            $this->applicationType = array_key_exists('TYPO3_REQUEST', $GLOBALS) && $GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface ? ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST']) : null;
        } catch (RuntimeException $e) {
            $this->applicationType = null;
        }

        $this->entrypointLookupCollection = $entrypointLookupCollection;
        $this->assetRegistry = $assetRegistry;
    }

    public function renderWebpackScriptTags(string $entryName, string $position = self::POSITION_FOOTER, string $buildName = EntrypointLookupInterface::DEFAULT_BUILD, PageRenderer $pageRenderer = null, array $parameters = [], bool $registerFile = true, bool $isLibrary = false): void
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = $pageRenderer ?? GeneralUtility::makeInstance(PageRenderer::class);
        $entryPointLookup = $this->getEntrypointLookup($buildName);

        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];
        $files = $entryPointLookup->getJavaScriptFiles($entryName);

        unset($parameters['file']);

        $wrapFirst = '';
        $wrapLast = '';
        $fileCount = count($files);
        if (!empty($parameters['allWrap']) && $fileCount > 1) {
            // If there are multiple files, allWrap should wrap all.
            // To do this, it's split up into two parts. The first part wraps the first file
            // and the second part wraps the last file.
            $splitChar = !empty($parameters['splitChar']) ? $parameters['splitChar'] : '|';
            $wrapArr = explode($splitChar, $parameters['allWrap'], 2);
            $wrapFirst = $wrapArr[0] . $splitChar;
            $wrapLast = $splitChar . $wrapArr[1];
            unset($parameters['allWrap']);
        }

        // We do not want to replace null values in $attributes
        $parameters = array_filter($parameters, static function ($param) {
            return !is_null($param);
        });

        foreach ($files as $index => $file) {
            $this->addAdditionalAbsRefPrefixDirectories($file);

            $allWrap = '';
            if (!$index) {
                // first file
                $allWrap = $wrapFirst;
            } elseif ($index === $fileCount - 1) {
                // last file
                $allWrap = $wrapLast;
            }

            $attributes = array_replace([
                'file' => $this->removeLeadingSlash($file, $parameters) ? ltrim($file, '/') : $file,
                'type' => $this->removeType($parameters) ? '' : 'text/javascript',
                'compress' => false,
                'forceOnTop' => false,
                'allWrap' => $allWrap,
                'excludeFromConcatenation' => true,
                'splitChar' => '|',
                'async' => false,
                'integrity' => $integrityHashes[$file] ?? '',
                'defer' => false,
                'crossorigin' => '',
            ], $parameters);

            $attributes = array_values($attributes);

            $pageRendererMethodName = 'addJS' . ($position === self::POSITION_FOOTER ? 'Footer' : '');

            if ($isLibrary) {
                $pageRendererMethodName .= 'Library';
                $filename = basename($file);
                $pageRenderer->{$pageRendererMethodName}($filename, ...$attributes);
            } else {
                $pageRendererMethodName .= 'File';
                $pageRenderer->{$pageRendererMethodName}(...$attributes);
            }

            if ($registerFile === true) {
                $this->assetRegistry->registerFile($file, 'script');
            }
        }
    }

    public function renderWebpackLinkTags(string $entryName, string $media = 'all', string $buildName = EntrypointLookupInterface::DEFAULT_BUILD, PageRenderer $pageRenderer = null, array $parameters = [], bool $registerFile = true): void
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = $pageRenderer ?? GeneralUtility::makeInstance(PageRenderer::class);
        $entryPointLookup = $this->getEntrypointLookup($buildName);
        $files = $entryPointLookup->getCssFiles($entryName);

        unset($parameters['file']);

        $wrapFirst = '';
        $wrapLast = '';
        $fileCount = count($files);
        if (!empty($parameters['allWrap']) && $fileCount > 1) {
            // If there are multiple files, allWrap should wrap all.
            // To do this, it's split up into two parts. The first part wraps the first file
            // and the second part wraps the last file.
            $splitChar = !empty($parameters['splitChar']) ? $parameters['splitChar'] : '|';
            $wrapArr = explode($splitChar, $parameters['allWrap'], 2);
            $wrapFirst = $wrapArr[0] . $splitChar;
            $wrapLast = $splitChar . $wrapArr[1];
            unset($parameters['allWrap']);
        }

        foreach ($files as $index => $file) {
            $this->addAdditionalAbsRefPrefixDirectories($file);

            $allWrap = '';
            if (!$index) {
                // first file
                $allWrap = $wrapFirst;
            } elseif ($index === $fileCount - 1) {
                // last file
                $allWrap = $wrapLast;
            }

            $attributes = array_replace([
                'file' => $this->removeLeadingSlash($file, $parameters) ? ltrim($file, '/') : $file,
                'rel' => 'stylesheet',
                'media' => $media,
                'title' => '',
                'compress' => false,
                'forceOnTop' => false,
                'allWrap' => $allWrap,
                'excludeFromConcatenation' => true,
                'splitChar' => '|',
                'inline' => false,
            ], $parameters);

            $attributes = array_values($attributes);

            $pageRenderer->addCssFile(...$attributes);

            if ($registerFile === true) {
                $this->assetRegistry->registerFile($file, 'style');
            }
        }
    }

    private function getEntrypointLookup(string $buildName): EntrypointLookupInterface
    {
        return $this->entrypointLookupCollection->getEntrypointLookup($buildName);
    }

    private function addAdditionalAbsRefPrefixDirectories(string $file): void
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['FE']['additionalAbsRefPrefixDirectories']) &&
            is_string($GLOBALS['TYPO3_CONF_VARS']['FE']['additionalAbsRefPrefixDirectories'])
        ) {
            $directories = GeneralUtility::trimExplode(
                ',',
                $GLOBALS['TYPO3_CONF_VARS']['FE']['additionalAbsRefPrefixDirectories'],
                true
            );

            $newDir = basename(dirname($file));

            if (false === in_array($newDir, $directories, true)) {
                $GLOBALS['TYPO3_CONF_VARS']['FE']['additionalAbsRefPrefixDirectories'] .= ',' . $newDir;
            }
        }
    }

    private function removeLeadingSlash($file, array $parameters): bool
    {
        if (array_key_exists('inline', $parameters) && (bool)$parameters['inline']) {
            return true;
        }

        if ($this->applicationType === null) {
            return false;
        }

        if (!$this->applicationType->isFrontend()) {
            return false;
        }

        if ($this->getTypoScriptFrontendController()->absRefPrefix === '') {
            return false;
        }

        if ($this->getTypoScriptFrontendController()->absRefPrefix === '/') {
            //avoid double //
            return true;
        }

        return !GeneralUtility::isValidUrl($file);
    }

    private function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    private function removeType(array $parameters): bool
    {
        if (array_key_exists('type', $parameters)) {
            return false;
        }

        if ($this->applicationType === null) {
            return false;
        }

        if (!$this->applicationType->isFrontend()) {
            return false;
        }

        if ($this->getTypoScriptFrontendController()->config['config']['doctype'] !== 'html5') {
            return false;
        }

        return true;
    }
}
