<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Resource\File;

// Configure plugin content element
$pluginSignature = 'pannellum_panorama';

// Add FlexForm
ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:pannellum/Configuration/FlexForms/Panorama.xml'
);

// Adjust tt_content subtype lists
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'pages,recursive';

// Add preview image FAL field for the plugin (shown only on our subtype)
// 1) Register TCA column
ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'tx_pannellum_preview' => [
        'exclude' => true,
        'label' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tt_content.tx_pannellum_preview',
        'description' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tt_content.tx_pannellum_preview.description',
        'config' => [
            'type' => 'file',
            'allowed' => 'common-image-types',
            'maxitems' => 1,
            'appearance' => [
                'createNewRelationLinkTitle' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:cm.createNewRelation',
            ],
            'overrideChildTca' => [
                'types' => [
                    '0' => [
                        'showitem' => '--palette--;;filePalette',
                    ],
                    File::FILETYPE_IMAGE => [
                        'showitem' => '--palette--;;filePalette',
                    ],
                ],
            ],
        ],
    ],
]);

// 2) Show the field only for our plugin subtype
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature]
    .= ',tx_pannellum_preview';
