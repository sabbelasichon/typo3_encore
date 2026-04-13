<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ValueObject;

final readonly class File
{
    public function __construct(
        private string $file,
        private FileType $fileType,
        private array $attributes = [],
        private string $rel = 'preload'
    ) {
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
