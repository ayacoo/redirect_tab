<?php

namespace Ayacoo\RedirectTab\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Unfortunately, the be.editRecord ViewHelper is not sufficient. Therefore, a custom ViewHelper that also provides the
 * page parameter for pagination in addition to the edit record.
 */
class RouteParametersViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('recordUid', 'integer', 'Record Uid', true, 0);
        $this->registerArgument('page', 'integer', 'Pagination Page', true, 0);
    }

    public function render(): array
    {
        return [
            'edit' => [
                'pages' => [
                    $this->arguments['recordUid'] => 'edit'
                ]
            ],
            'page' => $this->arguments['page']
        ];
    }
}
