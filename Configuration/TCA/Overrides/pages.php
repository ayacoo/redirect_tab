<?php

defined('TYPO3_MODE') || die();

(static function ($extKey, $table): void {

    /**
     * Extend TCA.
     */
    $temporaryColumns = [
        'redirects' => [
            'exclude' => true,
            'label' => '',
            'config' => [
                'type' => 'user',
                'renderType' => 'listRedirects',
            ],
        ],
    ];

    // Add to TCA pages
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        $table,
        $temporaryColumns
    );


    // Add additional tab for page properties
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        '--div--;LLL:EXT:redirect_tab/Resources/Private/Language/locallang.xlf:tab,' . implode(',', array_keys($temporaryColumns)),
        '',
        ''
    );
})('redirect_tab', 'pages');
