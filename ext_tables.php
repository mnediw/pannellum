<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Register the plugin for the backend (list_type)
ExtensionUtility::registerPlugin(
    'Pannellum',
    'Panorama',
    '360Grad Panorama'
);

// Add FlexForm to plugin
$pluginSignature = 'pannellum_panorama';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:pannellum/Configuration/FlexForms/Panorama.xml'
);

// Allow custom table on standard pages
ExtensionManagementUtility::allowTableOnStandardPages('tx_pannellum_scene');
