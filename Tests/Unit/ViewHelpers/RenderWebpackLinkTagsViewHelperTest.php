<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\PhpUnit\ProphecyTrait;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\TagRendererInterface;
use Ssch\Typo3Encore\ValueObject\LinkTag;
use Ssch\Typo3Encore\ViewHelpers\RenderWebpackLinkTagsViewHelper;

final class RenderWebpackLinkTagsViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    protected RenderWebpackLinkTagsViewHelper $viewHelper;

    /**
     * @var TagRendererInterface|MockObject
     */
    protected $tagRenderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRenderer = $this->getMockBuilder(TagRendererInterface::class)->getMock();
        $this->viewHelper = new RenderWebpackLinkTagsViewHelper($this->tagRenderer);
    }

    public function testRender(): void
    {
        $this->setArgumentsUnderTest($this->viewHelper, [
            'entryName' => 'app',
            'media' => 'all',
            'buildName' => EntrypointLookupInterface::DEFAULT_BUILD,
            'parameters' => [],
            'registerFile' => true,
        ]);
        $linkTag = new LinkTag('app', 'all', EntrypointLookupInterface::DEFAULT_BUILD, null, []);
        $this->tagRenderer->expects(self::once())->method('renderWebpackLinkTags')->with($linkTag);
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
