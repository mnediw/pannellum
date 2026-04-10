<?php

defined('TYPO3') or die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'iconfile' => 'EXT:pannellum/Resources/Public/Icons/Extension.svg',
        'searchFields' => 'identifier,title,panorama',
        'typeicon_classes' => [
            'default' => 'extension-pannellum',
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid,l10n_parent,l10n_diffsource, hidden, title, identifier, type, panorama, hotspot_debug, hotspots, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, starttime, endtime',
        ],
    ],
    'palettes' => [],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_pannellum_scene',
                'foreign_table_where' => 'AND {#tx_pannellum_scene}.{#pid}=###CURRENT_PID### AND {#tx_pannellum_scene}.{#sys_language_uid} IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    ['label' => '', 'invertStateDisplay' => true],
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

        'identifier' => [
            'exclude' => false,
            'label' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.identifier',
            'description' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.identifier.description',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,alphanum_x,required',
                'size' => 30,
            ],
        ],
        'title' => [
            'exclude' => false,
            'label' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.title',
            'description' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.title.description',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'size' => 50,
            ],
        ],
        'type' => [
            'exclude' => false,
            'label' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.type',
            'description' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.type.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['equirectangular', 'equirectangular'],
                    ['cubemap', 'cubemap'],
                ],
                'default' => 'equirectangular',
            ],
        ],
        'panorama' => [
            'exclude' => false,
            'label' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.panorama',
            'description' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.panorama.description',
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
                        TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '--palette--;;filePalette',
                        ],
                    ],
                ],
            ],
        ],
        'hotspot_debug' => [
            'exclude' => true,
            'label' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.hotSpotDebug',
            'description' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.hotSpotDebug.description',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'hotspots' => [
            'exclude' => false,
            'label' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.hotspots',
            'description' => 'LLL:EXT:pannellum/Resources/Private/Language/locallang_db.xlf:tx_pannellum_scene.hotspots.description',
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => 'FILE:EXT:pannellum/Configuration/FlexForms/SceneHotspots.xml',
                ],
            ],
        ],
    ],
];
