<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Asset;

use Ssch\Typo3Encore\ValueObject\LinkTag;
use Ssch\Typo3Encore\ValueObject\ScriptTag;
use TYPO3\CMS\Core\SingletonInterface;

interface TagRendererInterface extends SingletonInterface
{
    /**
     * @var array
     */
    public const ALLOWED_CSS_POSITIONS = ['cssLibs', 'cssFiles'];

    /**
     * @var array
     */
    public const ALLOWED_JS_POSITIONS = ['jsLibs', 'jsFiles'];

    /**
     * @var string
     */
    public const POSITION_FOOTER = 'footer';

    /**
     * @var string
     */
    public const POSITION_JS_LIBRARY = 'jsLibs';

    public function renderWebpackScriptTags(ScriptTag $scriptTag): void;

    public function renderWebpackLinkTags(LinkTag $linkTag): void;
}
