<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Redirect Tab',
    'description' => 'Show TYPO3 redirects in the page properties',
    'category' => 'plugin',
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '5.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.0.0-14.4.99',
            'redirects' => '14.0.0-14.4.99',
            'php' => '8.2.0-8.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
