<?php
defined('TYPO3_MODE') || die();

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['1610832584'] = [
        'nodeName' => 'listRedirects',
        'priority' => 40,
        'class' => \Ayacoo\RedirectTab\Form\Element\RedirectElement::class,
    ];
})();
