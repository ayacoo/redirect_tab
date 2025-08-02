<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Redirect Tab',
    'description' => 'Show TYPO3 redirects in the page properties',
    'category' => 'plugin',
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '4.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
            'redirects' => '13.0.0-13.4.99',
            'php' => '8.2.0-8.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
