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

    private string $type;

    private array $attributes;

    private string $rel;

    public function __construct(string $file, string $type, array $attributes = [], string $rel = 'preload')
    {
        $this->file = $file;
        $this->type = $type;
        $this->attributes = $attributes;
        $this->rel = $rel;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getType(): string
    {
        return $this->type;
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
