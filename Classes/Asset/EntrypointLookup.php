<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Asset;

use InvalidArgumentException;
use Ssch\Typo3Encore\Integration\FilesystemInterface;
use Ssch\Typo3Encore\Integration\JsonDecodeException;
use Ssch\Typo3Encore\Integration\JsonDecoderInterface;

final class EntrypointLookup implements EntrypointLookupInterface, IntegrityDataProviderInterface
{
    private ?array $entriesData = null;

    private string $entrypointJsonPath;

    private array $returnedFiles = [];

    private JsonDecoderInterface $jsonDecoder;

    private FilesystemInterface $filesystem;

    private bool $strictMode;

    public function __construct(
        string $entrypointJsonPath,
        bool $strictMode,
        JsonDecoderInterface $jsonDecoder,
        FilesystemInterface $filesystem
    ) {
        $this->entrypointJsonPath = $filesystem->getFileAbsFileName($entrypointJsonPath);
        $this->jsonDecoder = $jsonDecoder;
        $this->filesystem = $filesystem;
        $this->strictMode = $strictMode;
    }

    public function getJavaScriptFiles(string $entryName): array
    {
        return $this->getEntryFiles($entryName, 'js');
    }

    public function getCssFiles(string $entryName): array
    {
        return $this->getEntryFiles($entryName, 'css');
    }

    public function getIntegrityData(): array
    {
        $entriesData = $this->getEntriesData();

        if (! array_key_exists('integrity', $entriesData)) {
            return [];
        }

        return $entriesData['integrity'];
    }

    /**
     * Resets the state of this service.
     */
    public function reset(): void
    {
        $this->returnedFiles = [];
    }

    private function getEntryFiles(string $entryName, string $key): array
    {
        $this->validateEntryName($entryName);
        $entriesData = $this->getEntriesData();

        if (! isset($entriesData['entrypoints'][$entryName][$key])) {
            // If we don't find the file type then just send back nothing.
            return [];
        }

        // make sure to not return the same file multiple times
        $entryFiles = $entriesData['entrypoints'][$entryName][$key];
        $newFiles = array_values(array_diff($entryFiles, $this->returnedFiles));
        $this->returnedFiles = array_merge($this->returnedFiles, $newFiles);

        return $newFiles;
    }

    private function validateEntryName(string $entryName): void
    {
        $entriesData = $this->getEntriesData();
        if (! isset($entriesData['entrypoints'][$entryName]) && $this->strictMode) {
            $withoutExtension = substr($entryName, 0, (int) strrpos($entryName, '.'));

            if (isset($entriesData['entrypoints'][$withoutExtension])) {
                throw new EntrypointNotFoundException(sprintf(
                    'Could not find the entry "%s". Try "%s" instead (without the extension).',
                    $entryName,
                    $withoutExtension
                ));
            }

            throw new EntrypointNotFoundException(sprintf(
                'Could not find the entry "%s" in "%s". Found: %s.',
                $entryName,
                $this->entrypointJsonPath,
                implode(', ', array_keys($entriesData))
            ));
        }
    }

    private function getEntriesData(): array
    {
        if (null !== $this->entriesData) {
            return $this->entriesData;
        }

        if (! $this->filesystem->exists($this->entrypointJsonPath)) {
            throw new InvalidArgumentException(sprintf(
                'Could not find the entrypoints file from Webpack: the file "%s" does not exist.',
                $this->entrypointJsonPath
            ));
        }

        try {
            $this->entriesData = $this->jsonDecoder->decode($this->filesystem->get($this->entrypointJsonPath));
        } catch (JsonDecodeException $e) {
            throw new InvalidArgumentException(sprintf(
                'There was a problem JSON decoding the "%s" file',
                $this->entrypointJsonPath
            ));
        }

        if (! isset($this->entriesData['entrypoints'])) {
            throw new InvalidArgumentException(sprintf(
                'Could not find an "entrypoints" key in the "%s" file',
                $this->entrypointJsonPath
            ));
        }

        return $this->entriesData;
    }
}
