<?php

use Ssch\Typo3Encore\Integration\CacheFactory;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;

if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function ($packageKey) {

    // Caching of user requests
    if (! is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][CacheFactory::CACHE_KEY])
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][CacheFactory::CACHE_KEY] = [
            'frontend' => VariableFrontend::class,
            'backend'  => SimpleFileBackend::class,
            'options'  => [],
        ];
    }

    $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    $dispatcher->connect(
        \TYPO3\CMS\Core\Resource\ResourceFactory::class,
        \TYPO3\CMS\Core\Resource\ResourceFactoryInterface::SIGNAL_PreProcessStorage,
        \Ssch\Typo3Encore\Aspect\ResourceFactorySlot::class,
        'jsonManifestVersionStrategy'
    );
}, 'typo3_encore');
