<?php


namespace Ssch\Typo3Encore\Asset;

interface EntrypointLookupInterface
{

    /**
     * @param string $entryName
     *
     * @return array
     */
    public function getJavaScriptFiles(string $entryName): array;

    /**
     * @param string $entryName
     *
     * @return array
     */
    public function getCssFiles(string $entryName): array;

    /**
     * Resets the state of this service.
     */
    public function reset();

}