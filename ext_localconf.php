<?php


if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(static function ($packageKey) {

    // Caching of user requests
    if (! is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\Ssch\Typo3Encore\Integration\CacheFactory::CACHE_KEY])
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][\Ssch\Typo3Encore\Integration\CacheFactory::CACHE_KEY] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
            'options' => [],
        ];
    }

    if (TYPO3_MODE === 'FE') {
        $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
        $dispatcher->connect(
            \Ssch\Typo3Encore\Integration\ResourceFactoryExtended::class,
            \Ssch\Typo3Encore\Integration\ResourceFactoryExtended::SIGNAL_PreProcessFileIdentifier,
            \Ssch\Typo3Encore\Aspect\ResourceFactorySlot::class,
            'jsonManifestVersionStrategy'
        );

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Resource\ResourceFactory::class] = [
            'className' => \Ssch\Typo3Encore\Integration\ResourceFactoryExtended::class
        ];

        $GLOBALS['TYPO3_CONF_VARS']['FE']['addAllowedPaths'] .= ',' . $packageKey;
    }

    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)->registerImplementation(\Ssch\Typo3Encore\Asset\VersionStrategyInterface::class, Ssch\Typo3Encore\Asset\JsonManifestVersionStrategy::class);

    // Enable for Frontend and Backend at the same time
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][$packageKey] = \Ssch\Typo3Encore\Integration\PageRendererHooks::class . '->renderPreProcess';
}, 'typo3_encore');
