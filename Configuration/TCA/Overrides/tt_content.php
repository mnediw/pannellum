<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Resource\FileType;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Register the plugin as its own content type (CType)
ExtensionUtility::registerPlugin(
    'Pannellum',
    'Panorama',
    '360Grad Panorama',
    'extension-pannellum'
);

$pluginSignature = 'pannellum_panorama';

// Register the preview image FAL field on tt_content
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
                    FileType::IMAGE->value => [
                        'showitem' => '--palette--;;filePalette',
                    ],
                ],
            ],
        ],
    ],
]);

// Define the editing form (showitem) for the plugin content type, including the
// preview image field and the FlexForm. The standard content-element palettes are
// provided by fluid_styled_content and are resolved when the form is rendered.
$GLOBALS['TCA']['tt_content']['types'][$pluginSignature] = [
    'showitem' => '
        --palette--;;general,
        --palette--;;headers,
        tx_pannellum_preview,
        pi_flexform,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
        --palette--;;frames,
        --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ',
    'columnsOverrides' => [
        'pi_flexform' => [
            'config' => [
                'ds' => 'FILE:EXT:pannellum/Configuration/FlexForms/Panorama.xml',
            ],
        ],
    ],
];
