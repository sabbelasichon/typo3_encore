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
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\ValueObject\File;
use Ssch\Typo3Encore\ViewHelpers\PrerenderViewHelper;

final class PrerenderViewHelperTest extends ViewHelperBaseTestcase
{
    use ProphecyTrait;

    protected PrerenderViewHelper $viewHelper;

    /**
     * @var AssetRegistryInterface|MockObject
     */
    protected $assetRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assetRegistry = $this->getMockBuilder(AssetRegistryInterface::class)->getMock();
        $this->viewHelper = new PrerenderViewHelper($this->assetRegistry);
    }

    public function testRegisterFileWithEmptyAttributes(): void
    {
        $this->setArgumentsUnderTest($this->viewHelper, [
            'uri' => 'file.css',
            'as' => 'style',
        ]);
        $this->assetRegistry->expects(self::once())->method('registerFile')->with(
            new File('file.css', 'style', [], 'prerender')
        );
        $this->viewHelper->initializeArgumentsAndRender();
    }

    public function testRegisterFileWithAdditionalAttributes(): void
    {
        $attributes = [
            'type' => 'something',
        ];
        $this->setArgumentsUnderTest($this->viewHelper, [
            'uri' => 'file.css',
            'as' => 'style',
            'attributes' => $attributes,
        ]);
        $this->assetRegistry->expects(self::once())->method('registerFile')->with(
            new File('file.css', 'style', $attributes, 'prerender')
        );
        $this->viewHelper->initializeArgumentsAndRender();
    }
}
