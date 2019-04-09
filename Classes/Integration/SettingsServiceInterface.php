<?php


namespace Ssch\Typo3Encore\Integration;


interface SettingsServiceInterface
{

    public function getSettings(): array;

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function getByPath(string $path);
}