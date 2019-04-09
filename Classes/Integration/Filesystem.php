<?php
declare(strict_types=1);


namespace Ssch\Typo3Encore\Integration;


use UnexpectedValueException;

final class Filesystem implements FilesystemInterface
{

    /**
     * @param string $pathToFile
     *
     * @return false|string
     */
    public function get(string $pathToFile): string
    {
        $data = file_get_contents($pathToFile);

        if (false === $data) {
            throw new UnexpectedValueException('Data could not be read from file %s', $file);
        }

        return $data;
    }

    public function exists(string $pathToFile): bool
    {
        return file_exists($pathToFile);
    }
}