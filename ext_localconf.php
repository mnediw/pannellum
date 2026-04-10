<?php

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// Register Extbase plugin
ExtensionUtility::configurePlugin(
    'Pannellum',
    'Panorama',
    [\Diw\Pannellum\Controller\PannellumController::class => 'show'],
    []
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

// Register icon
/** @var IconRegistry $iconRegistry */
$iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
$iconRegistry->registerIcon(
    'extension-pannellum',
    SvgIconProvider::class,
    ['source' => 'EXT:pannellum/Resources/Public/Icons/Extension.svg']
);

// Add PageTS for new content element wizard
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    "@import 'EXT:pannellum/Configuration/PageTS/ContentElementWizard.typoscript'"
);
