<?php


namespace Ssch\Typo3Encore\Asset;


interface VersionStrategyInterface
{

    /**
     * Returns the asset version for an asset.
     *
     * @param string $path A path
     *
     * @return string The version string|null
     */
    public function getVersion($path): ?string;

    /**
     * Applies version to the supplied path.
     *
     * @param string $path A path
     *
     * @return string|null The versionized path
     */
    public function applyVersion($path): ?string;

}