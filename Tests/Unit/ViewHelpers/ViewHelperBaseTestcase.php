<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_encore" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Typo3Encore\Tests\Unit\ViewHelpers;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperVariableContainer;

abstract class ViewHelperBaseTestcase extends UnitTestCase
{
    use ProphecyTrait;

    /**
     * @var ViewHelperVariableContainer|ObjectProphecy
     */
    protected ObjectProphecy $viewHelperVariableContainer;

    /**
     * @var StandardVariableProvider
     */
    protected $templateVariableContainer;

    /**
     * @var ObjectProphecy|ControllerContext
     */
    protected $controllerContext;

    /**
     * @var TagBuilder|ObjectProphecy
     */
    protected $tagBuilder;

    protected array $arguments;

    /**
     * @var Request|ObjectProphecy
     */
    protected $request;

    /**
     * @var RenderingContext|ObjectProphecy
     */
    protected $renderingContext;

    protected function setUp(): void
    {
        $this->viewHelperVariableContainer = $this->prophesize(ViewHelperVariableContainer::class);
        $this->templateVariableContainer = $this->createMock(StandardVariableProvider::class);
        $this->request = $this->prophesize(Request::class);
        $this->controllerContext = $this->prophesize(ControllerContext::class);
        $this->controllerContext->getRequest()
            ->willReturn($this->request);
        $this->arguments = [];
        $this->renderingContext = $this->prophesize(RenderingContext::class);
        $this->renderingContext->setVariableProvider($this->templateVariableContainer);
        $this->renderingContext->getVariableProvider()
            ->willReturn($this->templateVariableContainer);
        $this->renderingContext->getViewHelperVariableContainer()
            ->willReturn($this->viewHelperVariableContainer);
        if (method_exists(RenderingContext::class, 'setRequest')) {
            $this->renderingContext->setRequest($this->request->reveal());
        }
        $this->renderingContext->injectViewHelperVariableContainer($this->viewHelperVariableContainer->reveal());
        $this->renderingContext->setControllerContext($this->controllerContext->reveal());
    }

    protected function injectDependenciesIntoViewHelper(ViewHelperInterface $viewHelper): void
    {
        $viewHelper->setRenderingContext($this->renderingContext->reveal());
        $viewHelper->setArguments($this->arguments);
    }

    /**
     * Helper function to merge arguments with default arguments according to their registration This usually happens in
     * ViewHelperInvoker before the view helper methods are called
     */
    protected function setArgumentsUnderTest(ViewHelperInterface $viewHelper, array $arguments = []): void
    {
        $argumentDefinitions = $viewHelper->prepareArguments();
        foreach ($argumentDefinitions as $argumentName => $argumentDefinition) {
            if (! isset($arguments[$argumentName])) {
                $arguments[$argumentName] = $argumentDefinition->getDefaultValue();
            }
        }
        $viewHelper->setArguments($arguments);
    }
}
