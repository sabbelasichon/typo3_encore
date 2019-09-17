<?php

namespace Ssch\Typo3Encore\Tests\Unit\Integration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Asset\EntrypointLookupCollectionInterface;
use Ssch\Typo3Encore\Asset\EntrypointLookupInterface;
use Ssch\Typo3Encore\Integration\PageRendererHooks;
use TYPO3\CMS\Core\Page\PageRenderer;

class PageRendererHooksTest extends UnitTestCase
{
    /**
     * @var PageRendererHooks
     */
    protected $subject;

    /**
     * @var EntrypointLookupCollectionInterface|MockObject
     */
    protected $entryLookupCollection;

    /**
     * @var MockObject|PageRenderer
     */
    protected $pageRenderer;

    protected function setUp()
    {
        $this->entryLookupCollection = $this->getMockBuilder(EntrypointLookupCollectionInterface::class)->getMock();
        $this->pageRenderer = $this->getMockBuilder(PageRenderer::class)->getMock();
        $this->subject = new PageRendererHooks($this->entryLookupCollection);
    }

    /**
     * @test
     */
    public function renderPreProcessWithDefaultBuild()
    {
        $params = [
            'jsFiles' => [
                [
                    'file' => 'typo3_encore:app',
                ],
            ],
            'cssFiles' => [
                [
                    'file' => 'typo3_encore:app',
                ],
            ],
        ];

        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->expects($this->once())->method('getJavaScriptFiles')->with('app')->willReturn(['files']);
        $entrypointLookup->expects($this->once())->method('getCssFiles')->with('app')->willReturn(['files']);

        $this->entryLookupCollection->expects($this->any())->method('getEntrypointLookup')->with('_default')->willReturn($entrypointLookup);
        $this->subject->renderPreProcess($params, $this->pageRenderer);
    }

    /**
     * @test
     */
    public function renderPreProcessWithDefinedBuild()
    {
        $params = [
            'jsFiles' => [
                [
                    'file' => 'typo3_encore:config:app',
                ],
            ],
            'cssFiles' => [
                [
                    'file' => 'typo3_encore:config:app',
                ],
            ],
        ];

        $entrypointLookup = $this->getMockBuilder(EntrypointLookupInterface::class)->getMock();
        $entrypointLookup->expects($this->once())->method('getJavaScriptFiles')->with('app')->willReturn(['files']);
        $entrypointLookup->expects($this->once())->method('getCssFiles')->with('app')->willReturn(['files']);

        $this->entryLookupCollection->expects($this->any())->method('getEntrypointLookup')->with('config')->willReturn($entrypointLookup);
        $this->subject->renderPreProcess($params, $this->pageRenderer);
    }
}
