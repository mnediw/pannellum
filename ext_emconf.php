<?php

$EM_CONF['pannellum'] = [
    'title' => 'Pannellum',
    'description' => 'Provides a plugin "360Grad Panorama" rendering Pannellum 360° panoramas.',
    'category' => 'plugin',
    'author' => 'Martin Neumann',
    'author_company' => 'www.die-internet-werkstatt.de',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.3.99',
            'typo3' => '12.4.0-13.4.99',
            'fluid' => '',
            'frontend' => '',
            'extbase' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
