<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Redirect Tab',
    'description' => 'Show TYPO3 redirects in the page properties',
    'category' => 'plugin',
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.9.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
