<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

(static function ($extKey, $table): void {

    $temporaryColumns = [
        'redirects' => [
            'exclude' => true,
            'label' => 'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:redirect_list',
            'config' => [
                'type' => 'user',
                'renderType' => 'listRedirects',
            ],
        ],
    ];

    // Add to TCA pages
    ExtensionManagementUtility::addTCAcolumns(
        $table,
        $temporaryColumns
    );


    // Add additional tab for page properties
    ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        '--div--;LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:tab,' . implode(',', array_keys($temporaryColumns)),
        '',
        ''
    );
})('redirect_tab', 'pages');
