<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ValueObject;

final class File
{
    private string $file;

    private array $attributes;

    private string $rel;

    private FileType $fileType;

    public function __construct(string $file, FileType $fileType, array $attributes = [], string $rel = 'preload')
    {
        $this->file = $file;
        $this->attributes = $attributes;
        $this->rel = $rel;
        $this->fileType = $fileType;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getType(): string
    {
        return $this->fileType->getType();
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getRel(): string
    {
        return $this->rel;
    }
}
