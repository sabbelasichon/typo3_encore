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

use Nimut\TestingFramework\TestCase\ViewHelperBaseTestcase;
use PHPUnit\Framework\MockObject\MockObject;
use Ssch\Typo3Encore\Integration\IdGeneratorInterface;
use Ssch\Typo3Encore\ViewHelpers\SvgViewHelper;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * @covers \Ssch\Typo3Encore\ViewHelpers\SvgViewHelper
 */
class SvgViewHelperTest extends ViewHelperBaseTestcase
{
    const ID = '2433esds';

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
    private $idGenerator;

    protected function setUp()
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
        $this->viewHelper->initializeArguments();
    }

    /**
     * @test
     *
     * @param array $arguments
     * @param string $expected
     * @dataProvider renderDataProvider
     */
    public function render(array $arguments, string $expected)
    {
        $arguments = array_merge($arguments, ['src' => 'somefile.svg']);
        $image = $this->getMockBuilder(FileInterface::class)->getMock();
        $this->viewHelper->setArguments($arguments);
        $this->imageService->expects($this->once())->method('getImage')->with($arguments['src'])->willReturn($image);
        $this->assertEquals($expected, $this->viewHelper->render());
    }

    public function renderDataProvider(): array
    {
        return [
            [
                ['name' => 'name'],
                sprintf('<svg xmlns="http://www.w3.org/2000/svg" focusable="false"><use xlink:href="#name" /></svg>')
            ],
            [
                ['name' => 1420],
                '<svg xmlns="http://www.w3.org/2000/svg" focusable="false"><use xlink:href="#1420" /></svg>'
            ],
            [
                ['name' => 3333, 'title' => 1222, 'description' => 1420],
                sprintf('<svg aria-labelledby="title-%1$s description-%1$s" xmlns="http://www.w3.org/2000/svg" focusable="false"><title id="title-%1$s">1222</title><desc id="description-%1$s">1420</desc><use xlink:href="#3333" /></svg>', self::ID)
            ],
            [
                ['name' => 'name', 'title' => 'Title', 'description' => 'Description'],
                sprintf('<svg aria-labelledby="title-%1$s description-%1$s" xmlns="http://www.w3.org/2000/svg" focusable="false"><title id="title-%1$s">Title</title><desc id="description-%1$s">Description</desc><use xlink:href="#name" /></svg>', self::ID)
            ],
        ];
    }
}
