<?php

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\PhpUnit\ProphecyTrait;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\ValueObject\File;
use Ssch\Typo3Encore\ViewHelpers\PreloadViewHelper;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\PreloadViewHelper
 */
final class PreloadViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    protected PreloadViewHelper $viewHelper;

    /**
     * @var AssetRegistryInterface|MockObject
     */
    protected $assetRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assetRegistry = $this->getMockBuilder(AssetRegistryInterface::class)->getMock();
        $this->viewHelper = new PreloadViewHelper($this->assetRegistry);
    }

    /**
     * @test
     */
    public function registerFileWithEmptyAttributes(): void
    {
        $this->setArgumentsUnderTest($this->viewHelper, ['uri' => 'file.css', 'as' => 'style']);
        $this->assetRegistry->expects(self::once())->method('registerFile')->with(new File('file.css', 'style', [], 'preload'));
        $this->viewHelper->initializeArgumentsAndRender();
    }

    /**
     * @test
     */
    public function registerFileWithAdditionalAttributes(): void
    {
        $attributes = ['type' => 'something'];
        $this->setArgumentsUnderTest($this->viewHelper, ['uri' => 'file.css', 'as' => 'style', 'attributes' => $attributes]);
        $this->assetRegistry->expects(self::once())->method('registerFile')->with(new File('file.css', 'style', $attributes, 'preload'));
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
