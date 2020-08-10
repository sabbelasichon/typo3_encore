<?php

namespace Ssch\Typo3Encore\Unit\ViewHelpers;

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

use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\IdGeneratorInterface;
use Ssch\Typo3Encore\ViewHelpers\SvgViewHelper;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\TestingFramework\Fluid\Unit\ViewHelpers\ViewHelperBaseTestcase;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\SvgViewHelper
 */
final class SvgViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var string
     */
    private const ID = '2433esds';

    /**
     * @var MockObject|SvgViewHelper
     */
    protected $viewHelper;

    /**
     * @var MockObject|TagBuilder
     */
    protected $tagBuilder;

    /**
     * @var ImageService|MockObject
     */
    protected $imageService;

    /**
     * @var IdGeneratorInterface|MockObject
     */
    protected $idGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->viewHelper = $this->getAccessibleMock(SvgViewHelper::class, ['renderChildren']);
        $this->imageService = $this->getMockBuilder(ImageService::class)->disableOriginalConstructor()->getMock();
        $this->idGenerator = $this->getMockBuilder(IdGeneratorInterface::class)->getMock();
        $this->idGenerator->method('generate')->willReturn(self::ID);
        $this->viewHelper->injectImageService($this->imageService);
        $this->viewHelper->injectIdGenerator($this->idGenerator);
        $this->tagBuilder = new TagBuilder('svg');
        $this->injectDependenciesIntoViewHelper($this->viewHelper);
    }

    /**
     * @test
     *
     * @param array $arguments
     * @param string $expected
     * @dataProvider renderDataProvider
     */
    public function render(array $arguments, string $expected): void
    {
        $arguments = array_merge($arguments, ['src' => 'somefile.svg']);
        $image = $this->getMockBuilder(FileInterface::class)->getMock();
        $image->method('getContents')->willReturn($this->getMockSvg());
        $this->setArgumentsUnderTest($this->viewHelper, $arguments);
        $this->imageService->expects($this->once())->method('getImage')->with($arguments['src'])->willReturn($image);
        $this->assertEquals($expected, $this->viewHelper->render());
    }

    public function renderDataProvider(): array
    {
        return [
            [
                ['name' => 'name'],
                sprintf('<svg xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><use xlink:href="#name" /></svg>')
            ],
            [
                ['name' => 1420],
                '<svg xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><use xlink:href="#1420" /></svg>'
            ],
            [
                ['name' => 3333, 'title' => 1222, 'description' => 1420],
                sprintf('<svg aria-labelledby="title-%1$s description-%1$s" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">1222</title><desc id="description-%1$s">1420</desc><use xlink:href="#3333" /></svg>', self::ID)
            ],
            [
                ['name' => 'name', 'title' => 'Title', 'description' => 'Description', 'width' => 100, 'height' => 100, 'role' => 'foo'],
                sprintf('<svg aria-labelledby="title-%1$s description-%1$s" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="foo"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc><use xlink:href="#name" /></svg>', self::ID)
            ],
            [
                ['name' => 'foobar', 'title' => 'Title', 'description' => 'Description', 'width' => 100, 'height' => 100, 'inline' => true],
                sprintf('<svg aria-labelledby="title-%1$s description-%1$s" viewBox="0 0 45 45" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc><circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red"/></svg>', self::ID)
            ],
            [
                ['name' => 'id-invalid', 'title' => 'Title', 'description' => 'Description', 'width' => 100, 'height' => 100, 'inline' => true],
                sprintf('<svg aria-labelledby="title-%1$s description-%1$s" width="100" height="100" xmlns="http://www.w3.org/2000/svg" focusable="false" role="img"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc></svg>', self::ID)
            ],
        ];
    }

    /**
     * @return string
     */
    private function getMockSvg(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><symbol viewBox="0 0 45 45" id="foobar"><circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red"/></symbol></defs><use id="foobar-usage" xlink:href="#foobar" class="sprite-symbol-usage"/></svg>';
    }
}
