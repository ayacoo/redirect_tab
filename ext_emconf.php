<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Redirect Tab',
    'description' => 'Show TYPO3 redirects in the page properties',
    'category' => 'plugin',
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '3.1.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.9.99',
            'redirects' => '12.4.0-12.9.99',
            'php' => '8.1.0-8.2.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
