<?php

$EM_CONF['pannellum'] = [
    'title' => 'Pannellum',
    'description' => 'Provides a plugin "360Grad Panorama" rendering Pannellum 360° panoramas.',
    'category' => 'plugin',
    'author' => 'Martin Neumann',
    'author_company' => 'www.die-internet-werkstatt.de',
    'state' => 'stable',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.4.99',
            'typo3' => '13.4.0-14.4.99',
            'fluid' => '',
            'frontend' => '',
            'extbase' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
