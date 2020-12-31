<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

use Prophecy\PhpUnit\ProphecyTrait;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Asset\TagRendererInterface;
use Ssch\Typo3Encore\ViewHelpers\RenderWebpackLinkTagsViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\RenderWebpackLinkTagsViewHelper
 */
final class RenderWebpackLinkTagsViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    /**
     * @var MockObject|RenderWebpackLinkTagsViewHelper
     */
    protected $viewHelper;

    /**
     * @var TagRendererInterface
     */
    protected $tagRenderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRenderer = $this->getMockBuilder(TagRendererInterface::class)->getMock();
        $this->viewHelper = new RenderWebpackLinkTagsViewHelper($this->tagRenderer);
    }

    /**
     * @test
     */
    public function render(): void
    {
        $this->setArgumentsUnderTest($this->viewHelper, ['entryName' => 'app', 'media' => 'all', 'buildName' => EntrypointLookupInterface::DEFAULT_BUILD, 'parameters' => [], 'registerFile' => true]);
        $this->tagRenderer->expects(self::once())->method('renderWebpackLinkTags')->with('app', 'all', EntrypointLookupInterface::DEFAULT_BUILD, null, []);
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
