<?php

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Information\Typo3Version;

// Register Extbase plugin
ExtensionUtility::configurePlugin(
    'Pannellum',
    'Panorama',
    [\Diw\Pannellum\Controller\PannellumController::class => 'show'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

// TypoScript setup for templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
    "
plugin.tx_pannellum {
  view {
    templateRootPaths.10 = EXT:pannellum/Resources/Private/Templates/
    partialRootPaths.10 = EXT:pannellum/Resources/Private/Partials/
    layoutRootPaths.10 = EXT:pannellum/Resources/Private/Layouts/
  }
}
    "
);

// Icon registration now happens via Configuration/Icons.php (TYPO3 v14:
// instantiating IconRegistry in ext_localconf.php is no longer allowed).

// Automatic inclusion of an extension's Configuration/page.tsconfig was introduced
// in TYPO3 v13. On older versions it must be imported explicitly, otherwise the
// content element wizard configuration is not loaded.
$versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
if ($versionInformation->getMajorVersion() < 13) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '@import "EXT:pannellum/Configuration/page.tsconfig"'
    );
}
