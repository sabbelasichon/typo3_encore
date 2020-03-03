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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ssch\Typo3Encore\Integration\AssetRegistryInterface;
use Ssch\Typo3Encore\Integration\SettingsServiceInterface;
use Symfony\Component\WebLink\GenericLinkProvider;
use Symfony\Component\WebLink\HttpHeaderSerializer;
use Symfony\Component\WebLink\Link;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

final class AssetsMiddleware implements MiddlewareInterface
{
    /**
     * @var array
     */
    private static $crossOriginAllowed = ['preload', 'preconnect'];

    /**
     * @var TypoScriptFrontendController
     */
    protected $controller;

    /**
     * @var AssetRegistryInterface
     */
    private $assetRegistry;

    /**
     * @var SettingsServiceInterface
     */
    private $settingsService;

    public function __construct(TypoScriptFrontendController $controller = null, AssetRegistryInterface $assetRegistry = null, SettingsServiceInterface $settingsService = null)
    {
        $this->controller = $controller ?? $GLOBALS['TSFE'];

        if (! $assetRegistry instanceof AssetRegistryInterface) {
            // @codeCoverageIgnoreStart
            /** @var AssetRegistryInterface $assetRegistry */
            $assetRegistry = GeneralUtility::makeInstance(ObjectManager::class)->get(AssetRegistryInterface::class);
            // @codeCoverageIgnoreEnd
        }

        if (! $settingsService instanceof SettingsServiceInterface) {
            // @codeCoverageIgnoreStart
            /** @var SettingsServiceInterface $settingsService */
            $settingsService = GeneralUtility::makeInstance(ObjectManager::class)->get(SettingsServiceInterface::class);
            // @codeCoverageIgnoreEnd
        }

        $this->settingsService = $settingsService;
        $this->assetRegistry = $assetRegistry;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (($response instanceof NullResponse && ! $this->controller->isOutputting())) {
            return $response;
        }

        if ($this->assetRegistry->getRegisteredFiles() === []) {
            return $response;
        }

        if (null === $linkProvider = $request->getAttribute('_links')) {
            $request = $request->withAttribute('_links', new GenericLinkProvider());
        }

        /** @var GenericLinkProvider $linkProvider */
        $linkProvider = $request->getAttribute('_links');
        $defaultAttributes = $this->assetRegistry->getDefaultAttributes();
        $crossOrigin = $defaultAttributes['crossorigin'] ? (bool)$defaultAttributes['crossorigin'] : false;

        foreach ($this->assetRegistry->getRegisteredFiles() as $rel => $relFiles) {
            // You can disable or enable one of the resource hints via typoscript simply by adding something like that preload.enable = 1, dns-prefetch.enable = 1
            if ($this->settingsService->getBooleanByPath(sprintf('%s.enable', $rel)) === false) {
                continue;
            }

            foreach ($relFiles['files'] as $type => $files) {
                foreach ($files as $href => $attributes) {
                    $link = (new Link($rel, PathUtility::getAbsoluteWebPath($href)))->withAttribute('as', $type);
                    if ($this->canAddCrossOriginAttribute($crossOrigin, $rel)) {
                        $link = $link->withAttribute('crossorigin', $crossOrigin);
                    }

                    foreach ($attributes as $key => $value) {
                        $link = $link->withAttribute($key, $value);
                    }

                    $linkProvider = $linkProvider->withLink($link);
                }
            }
        }

        $request = $request->withAttribute('_links', $linkProvider);

        /** @var GenericLinkProvider $linkProvider */
        $linkProvider = $request->getAttribute('_links');

        if ($linkProvider->getLinks() !== []) {
            $response = $response->withHeader('Link', (new HttpHeaderSerializer())->serialize($linkProvider->getLinks()));
        }

        return $response;
    }

    private function canAddCrossOriginAttribute(bool $crossOrigin, string $rel): bool
    {
        return false !== $crossOrigin && '' !== (string)$crossOrigin && in_array($rel, self::$crossOriginAllowed, true);
    }
}
