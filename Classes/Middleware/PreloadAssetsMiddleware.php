<?php
declare(strict_types = 1);

namespace Ssch\Typo3Encore\Middleware;

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

use Fig\Link\GenericLinkProvider;
use Fig\Link\Link;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ssch\Typo3Encore\Asset\TagRendererInterface;
use Symfony\Component\WebLink\HttpHeaderSerializer;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

final class PreloadAssetsMiddleware implements MiddlewareInterface
{

    /**
     * @var TypoScriptFrontendController
     */
    protected $controller;

    /**
     * @var TagRendererInterface|null
     */
    private $tagRenderer;

    /**
     * PreloadAssetsMiddleware constructor.
     *
     * @param TypoScriptFrontendController|null $controller
     * @param object|TagRendererInterface|null $tagRenderer
     */
    public function __construct(TypoScriptFrontendController $controller = null, TagRendererInterface $tagRenderer = null)
    {
        $this->controller = $controller ?? $GLOBALS['TSFE'];

        if (!$tagRenderer instanceof TagRendererInterface) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $tagRenderer = $objectManager->get(TagRendererInterface::class);
        }

        $this->tagRenderer = $tagRenderer;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if ($response instanceof NullResponse && ! $this->controller->isOutputting()) {
            return $response;
        }

        if ($this->tagRenderer->getRenderedScripts() === [] && $this->tagRenderer->getRenderedStyles() === []) {
            return $response;
        }

        if (null === $linkProvider = $request->getAttribute('_links')) {
            $request = $request->withAttribute('_links', new GenericLinkProvider());
        }

        /** @var GenericLinkProvider $linkProvider */
        $linkProvider = $request->getAttribute('_links');
        $defaultAttributes = $this->tagRenderer->getDefaultAttributes();
        $crossOrigin = $defaultAttributes['crossorigin'] ?? false;

        foreach ($this->tagRenderer->getRenderedScripts() as $href) {
            $link = (new Link('preload', PathUtility::getAbsoluteWebPath($href)))->withAttribute('as', 'script');
            if (false !== $crossOrigin) {
                $link = $link->withAttribute('crossorigin', $crossOrigin);
            }
            $linkProvider = $linkProvider->withLink($link);
        }

        foreach ($this->tagRenderer->getRenderedStyles() as $href) {
            $link = (new Link('preload', PathUtility::getAbsoluteWebPath($href)))->withAttribute('as', 'style');
            if (false !== $crossOrigin) {
                $link = $link->withAttribute('crossorigin', $crossOrigin);
            }
            $linkProvider = $linkProvider->withLink($link);
        }

        $request = $request->withAttribute('_links', $linkProvider);

        /** @var GenericLinkProvider $linkProvider */
        $linkProvider = $request->getAttribute('_links');

        if ($linkProvider->getLinks() !== []) {
            $response = $response->withHeader('Link', (new HttpHeaderSerializer())->serialize($linkProvider->getLinks()));
        }

        return $response;
    }
}
