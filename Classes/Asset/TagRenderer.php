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
use Ssch\Typo3Encore\ValueObject\File;
use Ssch\Typo3Encore\ValueObject\FileType;
use Ssch\Typo3Encore\ValueObject\LinkTag;
use Ssch\Typo3Encore\ValueObject\ScriptTag;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

final class TagRenderer implements TagRendererInterface
{
    private EntrypointLookupCollectionInterface $entrypointLookupCollection;

    private AssetRegistryInterface $assetRegistry;

    private ?ApplicationType $applicationType = null;

    public function __construct(
        EntrypointLookupCollectionInterface $entrypointLookupCollection,
        AssetRegistryInterface $assetRegistry
    ) {
        try {
            $this->applicationType = array_key_exists(
                'TYPO3_REQUEST',
                $GLOBALS
            ) && $GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface ? ApplicationType::fromRequest(
                $GLOBALS['TYPO3_REQUEST']
            ) : null;
        } catch (RuntimeException $e) {
            $this->applicationType = null;
        }

        $this->entrypointLookupCollection = $entrypointLookupCollection;
        $this->assetRegistry = $assetRegistry;
    }

    public function renderWebpackScriptTags(ScriptTag $scriptTag): void
    {
        $parameters = $scriptTag->getParameters();

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = $scriptTag->getPageRenderer() ?? GeneralUtility::makeInstance(PageRenderer::class);
        $entryPointLookup = $this->getEntrypointLookup($scriptTag->getBuildName());

        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];
        $files = $entryPointLookup->getJavaScriptFiles($scriptTag->getEntryName());

        unset($parameters['file']);

        $wrapFirst = '';
        $wrapLast = '';
        $fileCount = count($files);
        if (! empty($parameters['allWrap']) && $fileCount > 1) {
            // If there are multiple files, allWrap should wrap all.
            // To do this, it's split up into two parts. The first part wraps the first file
            // and the second part wraps the last file.
            $splitChar = ! empty($parameters['splitChar']) ? $parameters['splitChar'] : '|';
            $wrapArr = explode($splitChar, $parameters['allWrap'], 2);
            $wrapFirst = $wrapArr[0] . $splitChar;
            $wrapLast = $splitChar . $wrapArr[1];
            unset($parameters['allWrap']);
        }

        // We do not want to replace null values in $attributes
        $parameters = array_filter($parameters, static fn ($param) => null !== $param);

        foreach ($files as $index => $file) {
            $this->addAdditionalAbsRefPrefixDirectories($file);

            $allWrap = '';
            if (! $index) {
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

            $pageRendererMethodName = 'addJS' . (self::POSITION_FOOTER === $scriptTag->getPosition() ? 'Footer' : '');

            if ($scriptTag->isLibrary()) {
                $pageRendererMethodName .= 'Library';
                $filename = basename($file);
                $pageRenderer->{$pageRendererMethodName}($filename, ...$attributes);
            } else {
                $pageRendererMethodName .= 'File';
                $pageRenderer->{$pageRendererMethodName}(...$attributes);
            }

            if (true === $scriptTag->isRegisterFile()) {
                $this->assetRegistry->registerFile(new File($file, FileType::createScript()));
            }
        }
    }

    public function renderWebpackLinkTags(LinkTag $linkTag): void
    {
        $parameters = $linkTag->getParameters();

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = $linkTag->getPageRenderer() ?? GeneralUtility::makeInstance(PageRenderer::class);
        $entryPointLookup = $this->getEntrypointLookup($linkTag->getBuildName());
        $files = $entryPointLookup->getCssFiles($linkTag->getEntryName());

        unset($parameters['file']);

        $wrapFirst = '';
        $wrapLast = '';
        $fileCount = count($files);
        if (! empty($parameters['allWrap']) && $fileCount > 1) {
            // If there are multiple files, allWrap should wrap all.
            // To do this, it's split up into two parts. The first part wraps the first file
            // and the second part wraps the last file.
            $splitChar = ! empty($parameters['splitChar']) ? $parameters['splitChar'] : '|';
            $wrapArr = explode($splitChar, $parameters['allWrap'], 2);
            $wrapFirst = $wrapArr[0] . $splitChar;
            $wrapLast = $splitChar . $wrapArr[1];
            unset($parameters['allWrap']);
        }

        foreach ($files as $index => $file) {
            $this->addAdditionalAbsRefPrefixDirectories($file);

            $allWrap = '';
            if (! $index) {
                // first file
                $allWrap = $wrapFirst;
            } elseif ($index === $fileCount - 1) {
                // last file
                $allWrap = $wrapLast;
            }

            $attributes = array_replace([
                'file' => $this->removeLeadingSlash($file, $parameters) ? ltrim($file, '/') : $file,
                'rel' => 'stylesheet',
                'media' => $linkTag->getMedia(),
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

            if (true === $linkTag->isRegisterFile()) {
                $this->assetRegistry->registerFile(new File($file, FileType::createStyle()));
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

            $newDir = basename(dirname($file)) . '/';

            if (false === in_array($newDir, $directories, true)) {
                $GLOBALS['TYPO3_CONF_VARS']['FE']['additionalAbsRefPrefixDirectories'] .= ',' . $newDir;
            }
        }
    }

    private function removeLeadingSlash(string $file, array $parameters): bool
    {
        if (array_key_exists('inline', $parameters) && (bool) $parameters['inline']) {
            return true;
        }

        if (null === $this->applicationType) {
            return false;
        }

        if (! $this->applicationType->isFrontend()) {
            return false;
        }

        if ('' === $this->getTypoScriptFrontendController()->absRefPrefix) {
            return false;
        }

        if ('/' === $this->getTypoScriptFrontendController()->absRefPrefix) {
            return true;
        }

        if (str_starts_with($file, $this->getTypoScriptFrontendController()->absRefPrefix)) {
            return false;
        }

        return ! GeneralUtility::isValidUrl($file);
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

        if (null === $this->applicationType) {
            return false;
        }

        if (! $this->applicationType->isFrontend()) {
            return false;
        }

        if (! isset($this->getTypoScriptFrontendController()->config['config']['doctype']) || 'html5' !== $this->getTypoScriptFrontendController()->config['config']['doctype']) {
            return false;
        }

        return true;
    }
}
