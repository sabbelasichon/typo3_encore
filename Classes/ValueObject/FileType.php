<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\ValueObject;

use Webmozart\Assert\Assert;

final class FileType
{
    /**
     * @var string
     */
    private const STYLE = 'style';

    /**
     * @var string
     */
    private const SCRIPT = 'script';

    /**
     * @var string
     */
    private const FONT = 'font';

    private string $type;

    private function __construct(string $type)
    {
        Assert::inArray($type, [self::STYLE, self::SCRIPT, self::FONT]);

        $this->type = $type;
    }

    public static function createStyle(): self
    {
        return new self(self::STYLE);
    }

    public static function createScript(): self
    {
        return new self(self::SCRIPT);
    }

    public static function createFromString(string $type): self
    {
        return new self($type);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
