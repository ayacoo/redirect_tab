<?php

use Ayacoo\RedirectTab\Form\Element\RedirectElement;

defined('TYPO3') || die();

(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['1610832584'] = [
        'nodeName' => 'listRedirects',
        'priority' => 40,
        'class' => RedirectElement::class,
    ];
})();
