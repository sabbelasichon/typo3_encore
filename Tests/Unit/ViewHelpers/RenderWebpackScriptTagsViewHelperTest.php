<?php

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

use Ssch\Typo3Encore\Asset\TagRendererInterface;
use Ssch\Typo3Encore\ViewHelpers\RenderWebpackScriptTagsViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\RenderWebpackScriptTagsViewHelper
 */
final class RenderWebpackScriptTagsViewHelperTest extends ViewHelperBaseTestcase
{
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
        $this->setArgumentsUnderTest($this->viewHelper, ['entryName' => 'app', 'position' => 'footer', 'buildName' => '_default', 'parameters' => [], 'registerFile' => true]);
        $this->tagRenderer->expects($this->once())->method('renderWebpackScriptTags')->with('app', 'footer', '_default');
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
