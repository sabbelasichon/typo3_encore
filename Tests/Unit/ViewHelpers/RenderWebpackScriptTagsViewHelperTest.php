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
use Ssch\Typo3Encore\ViewHelpers\RenderWebpackScriptTagsViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\RenderWebpackScriptTagsViewHelper
 */
final class RenderWebpackScriptTagsViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    /**
     * @var MockObject|RenderWebpackScriptTagsViewHelper
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
        $this->viewHelper = new RenderWebpackScriptTagsViewHelper($this->tagRenderer);
    }

    /**
     * @test
     */
    public function render(): void
    {
        $this->setArgumentsUnderTest($this->viewHelper, ['entryName' => 'app', 'position' => 'footer', 'buildName' => EntrypointLookupInterface::DEFAULT_BUILD, 'parameters' => [], 'registerFile' => true]);
        $this->tagRenderer->expects(self::once())->method('renderWebpackScriptTags')->with('app', 'footer', EntrypointLookupInterface::DEFAULT_BUILD);
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
