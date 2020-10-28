<?php

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\ViewHelpers\DnsPrefetchViewHelper;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;

TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\DnsPrefetchViewHelper
 */
class DnsPrefetchViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var DnsPrefetchViewHelper
     */
    protected $viewHelper;

    /**
     * @var AssetRegistryInterface
     */
    protected $assetRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assetRegistry = $this->getMockBuilder(AssetRegistryInterface::class)->getMock();
        $this->viewHelper = new DnsPrefetchViewHelper($this->assetRegistry);
    }

    /**
     * @test
     */
    public function registerFileWithEmptyAttributes(): void
    {
        $this->setArgumentsUnderTest($this->viewHelper, ['uri' => 'file.css', 'as' => 'style']);
        $this->assetRegistry->expects($this->once())->method('registerFile')->with('file.css', 'style', [], 'dns-prefetch');
        $this->viewHelper->initializeArgumentsAndRender();
    }

    /**
     * @test
     */
    public function registerFileWithAdditionalAttributes(): void
    {
        $attributes = ['type' => 'something'];
        $this->setArgumentsUnderTest($this->viewHelper, ['uri' => 'file.css', 'as' => 'style', 'attributes' => $attributes]);
        $this->assetRegistry->expects($this->once())->method('registerFile')->with('file.css', 'style', $attributes, 'dns-prefetch');
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
