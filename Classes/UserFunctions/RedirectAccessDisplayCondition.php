<?php

declare(strict_types=1);

namespace Ayacoo\RedirectTab\UserFunctions;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class RedirectAccessDisplayCondition
{
    private const TABLE_NAME = 'sys_redirect';

    public function canListRedirects(): bool
    {
        return $this->getBackendUser()->isAdmin() || $this->getBackendUser()->check('tables_select', self::TABLE_NAME);
    }

    public function canEditRedirects(): bool
    {
        return $this->getBackendUser()->isAdmin() || $this->getBackendUser()->check('tables_modify', self::TABLE_NAME);
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
