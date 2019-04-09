<?php


namespace Ssch\Typo3Encore\Integration;


interface FilesystemInterface
{

    public function get(string $pathToFile): string;

    public function exists(string $pathToFile): bool;

}